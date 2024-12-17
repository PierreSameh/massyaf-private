<?php

if (!function_exists('responseApi'))
{
    function responseApi(int $status,string $message, $data=null)
    {
        $response = [
            'status' => $status,
            'message' => $message,
        ];

        if ($data) {
            $response['data'] = $data;
        }
        return response()->json($response, $status);
    }
}
