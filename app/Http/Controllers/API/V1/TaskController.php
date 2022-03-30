<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskParamsRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskCollection;
use App\Models\Task;
use App\Search\TaskSearch;

class TaskController extends Controller
{

    use ApiResponse;

    /**
     * Task - List Tasks
     * 
     * Display a listing of the tasks. (archived are hidden)
     * 
     * Archived tasks are hidden
     * /GET Tasks
     * 
     * **Archived Tasks will only appear if parameters archived_at_from is present**
     * 
     * ## NOTE THE BODY PARAMS BELOW ARE QUERYPARAMS
     * 
     * @return \Illuminate\Http\Response
     * @header Authorization Bearer 4|Ikzlly2IHmq81rPgq6a7S18jRHtI0AUV32orS5sG
     * @authenticated
     * @response {
     *     "current_page": 1,
     *     "data": [
     *         {
     *             "id": 36,
     *             "user_id": 3,
     *             "title": "Lorem Ipsum",
     *             "description": "Dolor Sit Amet",
     *             "due_date": "2022-06-07",
     *             "priority": "Low",
     *             "completed_at": null,
     *             "created_at": "2022-03-28T22:57:27.000000Z",
     *             "updated_at": "2022-03-28T23:26:10.000000Z",
     *             "archived_at": null,
     *             "deleted_at": null,
     *             "task_order": 37,
     *             "completed": false,
     *             "archived": false,
     *             "tags": [],
     *             "media": []
     *         },
     *     ],
     *     "first_page_url": "/api/v1/tasks?page=1",
     *     "from": 1,
     *     "last_page": 1,
     *     "last_page_url": "/api/v1/tasks?page=1",
     *     "links": [],
     *     "next_page_url": null,
     *     "path": "/api/v1/tasks",
     *     "per_page": 10,
     *     "prev_page_url": null,
     *     "to": 6,
     *     "total": 6
     * }
     * 
     */
    public function index(TaskParamsRequest $request)
    {
        $data = $request->validated();
        
        // apply filters available in the request object
        $query = (new TaskSearch)->apply($request);
        $data = new TaskCollection($query->paginate(10)->withQueryString());

        // attach pagination, include 200 on return
        return $this->success($data->response()->getData(true));
    }

    /**
     * Task - Create
     * 
     * Store a newly created task 
     * 
     * Can store tags (array)
     * 
     * Can store Multiple Media 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @bodyParam tags   String[] Can include tags to this task. e.g. ['Electronics', 'Kitchen']
     * @bodyParam media  File[]  Multiple file upload. Allowed Mime Types <br/>jpg, bmp, png, mp4, svg, jpeg, csv, txt, doc, docx
     * @header Authorization Bearer 4|Ikzlly2IHmq81rPgq6a7S18jRHtI0AUV32orS5sG
     * @authenticated
     * 
     * @response {
     *     "message": "Task created successfully.",
     *     "data": {
     *         "title": "Urgent",
     *         "description": "Lorem Ipsum Dolor",
     *         "due_date": "2022-06-03",
     *         "priority": "Urgent",
     *         "updated_at": "2022-03-28T23:59:23.000000Z",
     *         "created_at": "2022-03-28T23:59:23.000000Z",
     *         "id": 42,
     *         "task_order": 43,
     *         "completed": false,
     *         "archived": false,
     *         "tags": [
     *             {
     *                 "id": 1,
     *                 "name": {
     *                     "en": "Electronics"
     *                 },
     *                 "slug": {
     *                     "en": "electronics"
     *                 },
     *                 "type": null,
     *                 "order_column": 1,
     *                 "created_at": "2022-03-27T21:43:14.000000Z",
     *                 "updated_at": "2022-03-27T21:43:14.000000Z",
     *                 "pivot": {
     *                     "taggable_id": 42,
     *                     "tag_id": 1,
     *                     "taggable_type": "App\\Models\\Task"
     *                 }
     *             }
     *         ],
     *         "media": []
     *     }
     * }
     */
    public function store(TaskCreateRequest $request)
    {
        $data = $request->validated();
        
        $data['user_id'] = auth()->user()->id;
        $task = Task::create($data);

        if($request->has('tags')){
            $tags = array_filter($request->tags);
            $task->syncTags($tags);
        }
 
        if ($request->hasFile('media')) {
            foreach($request->media as $file){
                $fileName = time() . '_'.str_replace(' ','',$file->getClientOriginalName());
                $task->addMedia($file->getPathname())
                     ->preservingOriginal()
                     ->usingFileName($fileName)
                     ->toMediaCollection();
            }
        }
        $task->load(['tags','media']);
        

        return $this->success(['message' => 'Task created successfully.', 'data' => new TaskCollection($task)]);
    }

    /**
     * Task - FindOne
     * 
     * Display the specified task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @header Authorization Bearer 4|Ikzlly2IHmq81rPgq6a7S18jRHtI0AUV32orS5sG
     * @authenticated
     * 
     * @response {
     *   "id": 37,
     *   "title": "Urgent Task",
     *   "description": "Lorem Ipsum Dolor",
     *   "due_date": "2022-06-03",
     *   "priority": "Urgent",
     *   "order": null,
     *   "completed_at": "2022-03-28T23:14:42.000000Z",
     *   "created_at": "2022-03-28T23:00:07.000000Z",
     *   "updated_at": "2022-03-28T23:14:42.000000Z",
     *   "archived_at": null,
     *   "task_order": 38,
     *   "completed": true,
     *   "archived": false
     * }
     */
    public function show(Task $task)
    {
        
        $this->authorizeForUser(auth()->user(), 'show', [$task]);
        $task->load(['tags','media']);
        return $this->success(new TaskCollection($task));
    }



    /**
     * Task - Update a Task
     * 
     * Update the specified task 
     * 
     * Can store tags (array), updating tags will remove the existing tags and use the one you sent
     * 
     * Can store Multiple Media 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @bodyParam tags String[] Can include tags to this task. e.g. ['Electronics', 'Kitchen']<br/><em>updating tags will remove the existing tags and use the one you sent</em>
     * @bodyParam media File[] Multiple file upload. Allowed Mime Types <br/>jpg, bmp, png, mp4, svg, jpeg, csv, txt, doc, docx
     * @header Authorization Bearer 4|Ikzlly2IHmq81rPgq6a7S18jRHtI0AUV32orS5sG
     * @authenticated
     * 
     * @response {
     *   "message": "Task updated successfully.",
     *   "data": {
     *     "id": 33,
     *     "title": "Low Task",
     *     "description": "The quick brown fox",
     *     "due_date": "2022-06-07",
     *     "priority": "High",
     *     "order": null,
     *     "completed_at": "2022-06-07T00:00:00.000000Z",
     *     "created_at": "2022-03-28T22:57:27.000000Z",
     *     "updated_at": "2022-03-29T00:04:58.000000Z",
     *     "archived_at": null,
     *     "task_order": 37,
     *     "completed": true,
     *     "archived": false,
     *     "tags": [],
     *     "media": []
     *   }
     * }
     * 
     */
    public function update(TaskUpdateRequest $request, Task $task)
    {
        $data = $request->validated();

        $this->authorizeForUser(auth()->user(), 'show', [$task]);
       
 
        $task->fill($data);
        $task->save();

        if($request->has('tags')){
            $tags = array_filter($request->tags);
            $task->syncTags($tags);
        }
 
        if ($request->hasFile('media')) {
            foreach($request->media as $file){
                $fileName = time() . '_'.str_replace(' ','',$file->getClientOriginalName());
                $task->addMedia($file->getPathname())
                     ->preservingOriginal()
                     ->usingFileName($fileName)
                     ->toMediaCollection();
            }
        }

        $task->load(['tags','media']);
        
        return $this->success(['message' => 'Task updated successfully.', 'data' => new TaskCollection($task)]);
    }

    /**
     * Task - Delete 
     * 
     * Remove the specified task 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @header Authorization Bearer 4|Ikzlly2IHmq81rPgq6a7S18jRHtI0AUV32orS5sG
     * @authenticated
     * 
     */
    public function destroy(Task $task)
    {
        $this->authorizeForUser(auth()->user(), 'show', [$task]);
        $task->delete();

        return $this->success(['Task deleted successfully']);
    }

    /**
     * Task - Mark as Complete 
     * 
     * Mark Task as Complete and set the complete date to NOW()
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @header Authorization Bearer 4|Ikzlly2IHmq81rPgq6a7S18jRHtI0AUV32orS5sG
     * @authenticated
     * 
     * @response {
     *   "message": "Task Marked as Completed.",
     *   "data": {
     *     "id": 36,
     *     "title": "Low Task",
     *     "description": "The quick brown fox",
     *     "due_date": "2022-06-07",
     *     "priority": "High",
     *     "order": null,
     *     "completed_at": "2022-03-29T00:10:06.000000Z",
     *     "created_at": "2022-03-28T22:57:27.000000Z",
     *     "updated_at": "2022-03-29T00:10:06.000000Z",
     *     "archived_at": null,
     *     "task_order": 37,
     *     "completed": true,
     *     "archived": false
     *   }
     * }

     */
    public function complete(Task $task)
    {
        
        $this->authorizeForUser(auth()->user(), 'show', [$task]);
       
        $task->completed_at = \Carbon\Carbon::now();
        $task->save();

        return $this->success(['message' => 'Task Marked as Completed.', 'data' => new TaskCollection($task)]);
    }

    /**
     * Task - Mark as Incomplete 
     * 
     * Mark Task as Incomplete, set the complete date to NULL
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @header Authorization Bearer 4|Ikzlly2IHmq81rPgq6a7S18jRHtI0AUV32orS5sG
     * @authenticated
     * 
     * {
     *   "message": "Task Marked as Incomplete.",
     *   "data": {
     *     "id": 36,
     *     "title": "Low Task",
     *     "description": "The quick brown fox",
     *     "due_date": "2022-06-07",
     *     "priority": "High",
     *     "order": null,
     *     "completed_at": null,
     *     "created_at": "2022-03-28T22:57:27.000000Z",
     *     "updated_at": "2022-03-29T00:10:43.000000Z",
     *     "archived_at": null,
     *     "task_order": 37,
     *     "completed": false,
     *     "archived": false
     *   }
     * }
     */
    public function incomplete(Task $task)
    {
      
        $this->authorizeForUser(auth()->user(), 'show', [$task]);
       
        $task->completed_at = NULL;
        $task->save();

        return $this->success(['message' => 'Task Marked as Incomplete.', 'data' => new TaskCollection($task)]);
    }

    /**
     * Task - Archive Task 
     * 
     * Archived a set the archive date to NOW()
     * The archive task will not appear in the listing and will not be available for update/editing
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @header Authorization Bearer 4|Ikzlly2IHmq81rPgq6a7S18jRHtI0AUV32orS5sG
     * @authenticated
     * 
     * @response {
     *   "message": "Task Archived.",
     *   "data": {
     *     "id": 36,
     *     "title": "Low Task",
     *     "description": "The quick brown fox",
     *     "due_date": "2022-06-07",
     *     "priority": "High",
     *     "order": null,
     *     "completed_at": "2022-06-07T00:00:00.000000Z",
     *     "created_at": "2022-03-28T22:57:27.000000Z",
     *     "updated_at": "2022-03-29T00:09:13.000000Z",
     *     "archived_at": "2022-03-29T00:09:13.000000Z",
     *     "task_order": 37,
     *     "completed": true,
     *     "archived": true
     *   }
     * }
     */
    public function archive(Task $task)
    {
        $this->authorizeForUser(auth()->user(), 'show', [$task]);
        $task->archive();

        return $this->success(['message' => 'Task Archived.', 'data' => new TaskCollection($task)]);
    }

    /**
     * Task - Unarchived Task 
     * 
     * Unarchived an previously archived task
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @header Authorization Bearer 4|Ikzlly2IHmq81rPgq6a7S18jRHtI0AUV32orS5sG
     * @authenticated
     * 
     * @response{
     *   "message": "Task Unarchived.",
     *   "data": {
     *     "id": 36,
     *     "user_id": 3,
     *     "title": "XAXAXA",
     *     "description": "XAXAXA",
     *     "due_date": "2022-06-07",
     *     "priority": "Low",
     *     "order": null,
     *     "completed_at": null,
     *     "created_at": "2022-03-28T22:57:27.000000Z",
     *     "updated_at": "2022-03-28T23:26:10.000000Z",
     *     "archived_at": null,
     *     "deleted_at": null,
     *     "task_order": 37,
     *     "completed": false,
     *     "archived": false
     *   }
     * }
     */
    public function unarchive($id)
    {
        $task = Task::query()->onlyArchived()->find($id);
        $this->authorizeForUser(auth()->user(), 'show', [$task]);
        $task->unarchive();

        return $this->success(['message' => 'Task Unarchived.', 'data' => new TaskCollection($task)]);
        
    }
}
