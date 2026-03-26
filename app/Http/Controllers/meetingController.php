<?php

namespace App\Http\Controllers;

use App\Models\customers;
use App\Models\MeetingParticipant;
use App\Models\meetings;
use App\Models\users;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class meetingController extends Controller
{
    public function index(Request $request)
    {
        $accessibleUserIds = Session::get('accessible_user_ids');
        $currentUserId = $request->user->id;
        $customers = customers::orderBy('name')->get();

        $users = users::get();
        $query = meetings::with(['organizer', 'customer', 'participants'])
            ->orderBy('start_time', 'desc');

        if ($accessibleUserIds && !in_array('all', $accessibleUserIds)) {
            $query->where(function ($q) use ($accessibleUserIds, $currentUserId) {
                $q->whereIn('organizer_id', $accessibleUserIds)
                    ->orWhereHas('participants', function ($participantQuery) use ($currentUserId) {
                        $participantQuery->where('user_id', $currentUserId);
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('meeting_type', $request->type);
        }

        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('start_time', today());
                    break;
                case 'tomorrow':
                    $query->whereDate('start_time', today()->addDay());
                    break;
                case 'this_week':
                    $query->whereBetween('start_time', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'next_week':
                    $query->whereBetween('start_time', [now()->addWeek()->startOfWeek(), now()->addWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereBetween('start_time', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
                case 'custom':
                    if ($request->filled('start_date')) {
                        $query->whereDate('start_time', '>=', $request->start_date);
                    }
                    if ($request->filled('end_date')) {
                        $query->whereDate('start_time', '<=', $request->end_date);
                    }
                    break;
            }
        }

        $meetings = $query->paginate(20);
        $statsQuery = meetings::query();
        if ($accessibleUserIds && !in_array('all', $accessibleUserIds)) {
            $statsQuery->whereIn('organizer_id', $accessibleUserIds);
        }

        $stats = [
            'total' => $statsQuery->count(),
            'scheduled' => $statsQuery->clone()->where('status', 'scheduled')->count(),
            'in_progress' => $statsQuery->clone()->where('status', 'in_progress')->count(),
            'completed' => $statsQuery->clone()->where('status', 'completed')->count(),
            'today' => $statsQuery->clone()->whereDate('start_time', today())->count(),
            'upcoming' => $statsQuery->clone()->where('start_time', '>', now())->where('status', 'scheduled')->count(),
        ];

        return view('meetings.index', compact('meetings', 'stats', 'customers',  'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable',
            'location' => 'nullable|string|max:255',
            'meeting_type' => 'required|in:internal,client,team,general',
            'meeting_link' => 'nullable|url',
            'customer_id' => 'nullable|exists:customers,id',
            'lead_id' => 'nullable|exists:leads,id',
            'participants' => 'nullable|array',
            'participants.*.type' => 'nullable',
            'participants.*.id' => 'nullable',
            'participants.*.email' => 'nullable',
            'participants.*.name' => 'nullable'
        ]);



        $meetingID =  DB::table('meetings')->insertGetId(array(
            'title' => $request->title,
            'description' => $request->description,

            'location' => $request->location,
            'meeting_type' => $request->meeting_type,
            'meeting_link' => $request->meeting_link,
            'organizer_id' => $request->user->id,
            'customer_id' => $request->customer_id,
            'lead_id' => $request->lead_id,
            'status' => 'scheduled'
        ));

        DB::table("meeting_participants")->insert(array(
            'meeting_id' => $meetingID,
            'user_id' => $request->user->id,
            'email' => $request->user->email,
            'name' => $request->user->name,
            'status' => 'accepted'
        ));

        if ($request->has('participants')) {
            foreach ($request->participants as $participant) {
                if ($participant['type'] === 'user') {
                    $user = users::find($participant['id']);
                    if ($user) {
                        MeetingParticipant::create([
                            'meeting_id' => $meetingID,
                            'user_id' => $user->id,
                            'email' => $user->email,
                            'name' => $user->name,
                            'status' => 'invited'
                        ]);
                    }
                } elseif ($participant['type'] === 'customer') {
                    $customer = customers::find($participant['id']);
                    if ($customer) {
                        MeetingParticipant::create([
                            'meeting_id' => $meetingID,
                            'customer_id' => $customer->id,
                            'email' => $customer->email,
                            'name' => $customer->name,
                            'status' => 'invited'
                        ]);
                    }
                } elseif ($participant['type'] === 'external') {
                    MeetingParticipant::create([
                        'meeting_id' => $meetingID,
                        'email' => $participant['email'],
                        'name' => $participant['name'],
                        'status' => 'invited'
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Meeting created successfully!',
            'meeting' => ""
        ]);
    }

    public function update(Request $request, meetings $meeting)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable',
            'location' => 'nullable|string|max:255',
            'meeting_type' => 'required|in:internal,client,team,general',
            'meeting_link' => 'nullable|url',
            'customer_id' => 'nullable|exists:customers,id',
            'lead_id' => 'nullable|exists:leads,id',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled'
        ]);



        DB::table('meetings')->where("id", $meeting->id)->update(array(
            'title' => $request->title,
            'description' => $request->description,

            'location' => $request->location,
            'meeting_type' => $request->meeting_type,
            'meeting_link' => $request->meeting_link,
            'organizer_id' => $request->user->id,
            'customer_id' => $request->customer_id,
            'lead_id' => $request->lead_id,
            'status' => $request->status
        ));

        return response()->json([
            'success' => true,
            'message' => 'Meeting updated successfully!',
            'meeting' => $meeting->load(['organizer', 'customer', 'participants'])
        ]);
    }

    public function destroy(meetings $meeting)
    {
        $meeting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Meeting deleted successfully!'
        ]);
    }

    public function updateStatus(Request $request, meetings $meeting)
    {
        $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,cancelled'
        ]);

        $meeting->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Meeting status updated successfully!'
        ]);
    }

    public function addParticipant(Request $request, meetings $meeting)
    {
        $request->validate([
            'type' => 'required|in:user,customer,external',
            'id' => 'required_if:type,user,customer',
            'email' => 'required_if:type,external|email',
            'name' => 'required_if:type,external|string'
        ]);

        if ($request->type === 'user') {
            $user = users::find($request->id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $participant = MeetingParticipant::create([
                'meeting_id' => $meeting->id,
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'status' => 'invited'
            ]);
        } elseif ($request->type === 'customer') {
            $customer = customers::find($request->id);
            if (!$customer) {
                return response()->json(['error' => 'Customer not found'], 404);
            }

            $participant = MeetingParticipant::create([
                'meeting_id' => $meeting->id,
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'name' => $customer->name,
                'status' => 'invited'
            ]);
        } else {
            $participant = MeetingParticipant::create([
                'meeting_id' => $meeting->id,
                'email' => $request->email,
                'name' => $request->name,
                'status' => 'invited'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Participant added successfully!',
            'participant' => $participant
        ]);
    }

    public function removeParticipant(MeetingParticipant $participant)
    {
        $participant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Participant removed successfully!'
        ]);
    }

    public function updateParticipantStatus(MeetingParticipant $participant, Request $request)
    {
        $request->validate([
            'status' => 'required|in:accepted,declined,tentative'
        ]);

        $participant->update([
            'status' => $request->status,
            'responded_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Response recorded successfully!'
        ]);
    }

    public function getMeetingDetails(meetings $meeting)
    {
        $meeting->load(['organizer', 'customer', 'participants.user', 'participants.customer']);

        return response()->json([
            'success' => true,
            'meeting' => $meeting
        ]);
    }

    public function startMeeting(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with('error', $error);

                $count++;
            }
        }

        try {



            DB::table('meetings')->where("id", $request->id)->update(array(
                "start_time" => now(),
                "start_location" => $request->location,
            ));
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function stopMeeting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with('error', $error);

                $count++;
            }
        }

        try {

            DB::table('meetings')->where("id", $request->id)->update(array(
                "end_time" => now(),
                "end_location" => $request->location,
                "remarks" => $request->remarks,
            ));
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }
}
