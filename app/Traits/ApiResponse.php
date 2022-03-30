<?php 

namespace App\Traits;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use JsonSerializable;

use function response;


trait ApiResponse
{
    private ?array $_defaultSuccessData = ['success' => true];

    /**
     * @param string|\Exception $message
     * @param  string|null  $key
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function notfound($message, string $key = 'error'): JsonResponse 
    {
        return $this->apiResponse(
            [$key => $this->_toString($message)],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @param array|Arrayable|JsonSerializable|null $contents
     */
    public function success($contents = null): JsonResponse
    {
        $contents = $this->_toArray($contents) ?? [];

        $data = [] === $contents
            ? $this->_defaultSuccessData
            : $contents;

        return $this->apiResponse($data);
    }

    public function setDefaultSuccessResponse(?array $content = null): self
    {
        $this->_defaultSuccessData = $content ?? [];
        return $this;
    }

    public function ok(string $message): JsonResponse
    {
        return $this->success(['success' => $message]);
    }

    public function unAuthenticated(?string $message = null): JsonResponse
    {
        return $this->apiResponse(
            ['error' => $message ?? 'Unauthenticated'],
            Response::HTTP_UNAUTHORIZED
        );
    }

    public function forbidden(?string $message = null): JsonResponse
    {
        return $this->apiResponse(
            ['error' => $message ?? 'Forbidden'],
            Response::HTTP_FORBIDDEN
        );
    }

    public function error(?string $message = null): JsonResponse
    {
        return $this->apiResponse(
            ['error' => $message ?? 'Error'],
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @param array|Arrayable|JsonSerializable|null $data
     */
    public function created($data = null): JsonResponse
    {
        $data ??= [];
        return $this->apiResponse(
          $this->_toArray($data),
          Response::HTTP_CREATED
        );
    }
    

    /**
     * @param array|Arrayable|JsonSerializable|null $data
     */
    public function respondNoContent($data = null): JsonResponse
    {
        $data ??= [];
        $data = $this->_toArray($data);

        return $this->apiResponse($data, Response::HTTP_NO_CONTENT);
    }

    private function apiResponse(array $data, int $code = 200): JsonResponse
    {
        return response()->json($data, $code);
    }

    /**
     * @param array|Arrayable|JsonSerializable|null $data
     * @return array|null
     */
    private function _toArray($data)
    {
        // dd(($data instanceof Arrayable));
        if ($data instanceof Arrayable) {
            return $data->toArray();
        }

        if ($data instanceof JsonSerializable) {
            return $data->jsonSerialize();
        }

        return $data;
    }

    /**
     * @param string|\Exception $message
     * @return string
     */
    private function _toString($message): string
    {
        return $message instanceof Exception
          ? $message->getMessage()
          : $message;
    }
}