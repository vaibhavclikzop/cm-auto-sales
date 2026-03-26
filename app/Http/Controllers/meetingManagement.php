<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class meetingManagement extends Controller
{
    public function Meetings(Request $request)
    {
        $data = DB::table("meetings as a")
            ->select("a.*", "b.name as user", "c.name as customer")
            ->join("users as b", "a.organizer_id", "b.id")
            ->join("customers as c", "a.customer_id", "c.id")
            ->where("a.organizer_id", $request->user->id)
            ->where("a.company_id", $request->user->active_inventory)
            ->orderBy("id", "desc")->get();
        return view("lead-app/meetings", compact("data"));
    }

    public function SaveMeeting(Request $request)
    {
        $validator = Validator::make($request->all(), [


            'title' => 'required',
            'customer_id' => 'required',

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


            if (empty($request->id)) {
                DB::table('meetings')->insert(array(
                    "title" => $request->title,
                    "description" => $request->description,
                    "meeting_type" => $request->meeting_type,
                    "meeting_link" => $request->meeting_link,
                    "location" => $request->location,
                    "organizer_id" => $request->user->id,
                    "customer_id" => $request->customer_id,
                    "company_id" => $request->user->active_inventory,

                ));
            } else {

                DB::table('meetings')->where("id", $request->id)->update(array(
                    "title" => $request->title,
                    "description" => $request->description,
                    "meeting_type" => $request->meeting_type,
                    "meeting_link" => $request->meeting_link,
                    "location" => $request->location,
                    "organizer_id" => $request->user->id,
                    "customer_id" => $request->customer_id,
                    "company_id" => $request->user->active_inventory,

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function StopMeeting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'location' => 'required',
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
                "status" => "completed",
            ));
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function startMeeting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'location' => 'required',
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
                "status" => "In Progress",
            ));
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }
}
