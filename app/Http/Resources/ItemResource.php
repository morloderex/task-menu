<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    protected $responseCode = 201;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    /**
     * @param array $data
     * @param int $responseCode
     * @return \App\Http\Resources\ItemResource
     */
    public static function make(...$data)
    {
        $self = parent::make(...$data);

        return $self->setStatusCode($data[1] ?? 200);
    }

    protected function setStatusCode(int $responseCode)
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    /**
     * Customize the response for a request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\JsonResponse  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $response->setStatusCode($this->responseCode);
    }
}
