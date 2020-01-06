<?php


namespace App\Api\Helpers;

use Response;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

trait ApiResponse
{

    protected $statusCode = FoundationResponse::HTTP_OK;

    /**
     * 获取状态码
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * 设置错误码
     * @param $statusCode
     * @param null $httpCode
     * @return $this
     */
    public function setStatusCode($statusCode, $httpCode = null)
    {
        $httpCode = $httpCode ?? $statusCode;
        $this->statusCode = $httpCode;

        return $this;
    }

    public function respond($data, $header = [])
    {
        return Response::json($data, $this->getStatusCode(), $header);
    }

    public function status($status, array $data, $code = null)
    {

        if ($code) {
            $this->setStatusCode($code);
        }
        $status = [
            'status' => $status,
            'code'   => $this->statusCode
        ];

        $data = array_merge($status, $data);

        return $this->respond($data);
    }

    public function message($message, $status = "success")
    {
        return $this->status($status, [
            'message' => $message
        ]);
    }

    /**
     * 200
     * @param $data
     * @param string $status
     * @return mixed
     */
    public function success($data, $status = "success")
    {
        return $this->status($status, compact('data'));
    }

    /**
     * 创建成功 201
     * @param $data
     * @param string $status
     * @return mixed
     */
    public function created($data, $status = "created")
    {
        return $this->status($status, compact('data'), FoundationResponse::HTTP_CREATED);
    }

    /**
     * 请求失败 400
     * @param $message
     * @param int $code
     * @param string $status
     * @return mixed
     */
    public function failed($message, $code = FoundationResponse::HTTP_BAD_REQUEST, $status = 'error')
    {
        return $this->setStatusCode($code)->message($message, $status);
    }

    /**
     * 找不到任何资源 404
     * @param string $message
     * @return mixed
     */
    public function notFond($message = 'Not Fond!')
    {
        return $this->failed($message, Foundationresponse::HTTP_NOT_FOUND);
    }

    /**
     * 验证错误 422
     * @param string $message
     * @return mixed
     */
    public function error($message = 'error')
    {
        return $this->failed($message, Foundationresponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * 服务器错误 500
     * @param string $message
     * @return mixed
     */
    public function internalError($message = "Internal Error!")
    {
        return $this->failed($message, FoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
