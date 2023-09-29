<?php

namespace App\Http\Controllers\api\v1;

use App\Classes\ChatGPT\ChatGPTClient;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectContentRequest;
use App\Models\Project;
use App\Models\ProjectEmbedding;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectEmbeddingController extends Controller
{
    /**
     * Store the project content from a txt file
     *
     * @param StoreProjectContentRequest $request
     * @param string $project_id
     *
     * @return JsonResponse
     */
    public function store(StoreProjectContentRequest $request, string $project_id): JsonResponse
    {
        try {
            $project = Project::find($project_id);

            if (!$project) {
                return response()->json(['message' => "Project not found."], 404);
            }

            $is_owner = $project->organization->owner->id === $request->user()->id;

            if (!$is_owner && $request->user()->role != 'admin') {
                return response()->json(['message' => "The current user is not the the organization owner."], 422);
            }

            return response()->json(['message' => "Content for project $project_id"], 500);

        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * get texts and store project embeddings.
     *
     * @param Request $request
     * @param string $project_id
     *
     * @return JsonResponse
     */
    public function store_embeddings(Request $request, string $project_id): JsonResponse
    {
        try {
            $project = Project::find($project_id);

            if (!$project) {
                return response()->json(['message' => 'Project not found.'], 404);
            }

            $chat_gpt_client = new ChatGPTClient();
            $embeddings = [];

            foreach ($request->data as $text) {
                $embeddings[] = [
                    'project_id'    => $project->id,
                    'text'          => $text,
                    'embedding'     => $chat_gpt_client->getEmbedding($text)
                ];
            }

            foreach ($embeddings as $embedding) {
                ProjectEmbedding::create($embedding);
            }

            return response()->json(['message' => 'Storing embeddings for project ' . $project->id], 200);

        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);

        }
    }
}
