<?php

namespace Modules\Task\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Task;
use App\Models\Note;
use Validator;
use Exception;
use DB;

class TaskController extends Controller
{
    public function listing(Request $request) {
        if(!is_null($request->filter)) {
            $status = array_key_exists("status", $request->filter) ? $request->filter["status"] : [];
            $due_date = array_key_exists("due_date", $request->filter) ? $request->filter["due_date"] : [];
            $priority = array_key_exists("priority", $request->filter) ? $request->filter["priority"] : [];
            $notes = array_key_exists("notes", $request->filter) ? $request->filter["notes"] : [];

            $tasks = Task::with('note')
                ->withCount('note')
                ->where('status', $status)
                ->where('due_date', '=', $due_date)
                ->where('priority', '=', $priority)
                ->having('note_count', '=', $notes)
                ->orderBy('note_count', 'desc')
                ->orderByRaw("FIELD(priority, 'High', 'Medium', 'Low')")
                ->get();
        } else {
            $tasks = Task::with('note')
                ->withCount('note')
                ->orderBy('note_count', 'desc')
                ->orderByRaw("FIELD(priority, 'High', 'Medium', 'Low')")
                ->get();
        }

        $response = [
            'status' => 'success',
            'message' => 'Task is created successfully.',
            'data'  => $tasks
        ];
        
        return response()->json($response, 201);
    }

    public function store(Request $request) {
        $validate = Validator::make($request->all(), [
            'subject' => 'required',
            'description' => 'required',
            'start_date' => 'required|date',
            'due_date' => 'required|date',
            'status' => 'required',
            'priority' => 'required',
            'note' => 'required|array',
        ]);
        
        if($validate->fails()){
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        
        DB::beginTransaction();
        try {
            $task = new Task();
            $task->subject = $request->subject;
            $task->description = $request->description;
            $task->start_date = $request->start_date;
            $task->due_date = $request->due_date;
            $task->status = $request->status;
            $task->priority = $request->priority;
            $task->save();

            foreach($request->note as $note) {
                $notes = new Note();
                $notes->subject = $note["subject"];
                $notes->note = $note["note"];
                
                $file = [];
                if(!is_null($note["attachment"])) {
                    foreach($note["attachment"] as $attachment) {
                        if(file_exists($attachment)) {
                            $fileName = time().'_'.$attachment->getClientOriginalName();
                            $filePath = $attachment->storeAs('uploads', $fileName, 'public');
                            $file_path = '/storage/' . $filePath;
                            $fileArray = [
                                "file_name" => $fileName,
                                "file_path" => $file_path
                            ];
                            array_push($file, $fileArray);
                        }
                    }
                }

                $notes->attachment = $file;
                $notes->task_id = $task->id;
                $notes->save();
            }
        } catch (Exception $error) {
            DB::rollBack();
        }

        DB::commit();
        

        $response = [
            'status' => 'success',
            'message' => 'Task is created successfully.',
        ];
        
        return response()->json($response, 201);
    }
}
