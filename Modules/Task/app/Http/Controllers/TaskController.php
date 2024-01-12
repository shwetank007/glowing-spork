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
    public function listing() {

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
