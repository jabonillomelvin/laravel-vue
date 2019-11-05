<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function submit(Request $request)
    {
        $this->validate($request, [
            'event' => 'required|string',
            'from' => 'required',
            'to' => 'required',
            'days' => 'required'
        ]);

        $data = $request->all();
        $data['from'] = date('Y-m-d', strtotime($data['from']));
        $data['to']   = date('Y-m-d', strtotime($data['to']));
        $data['days_selected'] = implode(',', $data['days']);

        \App\Calendar::create($data);

        return response()->json($data, 200);
    }
}
