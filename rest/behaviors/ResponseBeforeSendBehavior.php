<?php

namespace rest\behaviors;

use yii\base\Behavior;
use Yii;

class ResponseBeforeSendBehavior extends Behavior
{
    const EVENT_RESPONSE_BEFORE_SEND = 'beforeSend';
    
    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            self::EVENT_RESPONSE_BEFORE_SEND => 'beforeSend'
        ];
    }

    /**
     * @param $event \yii\web\UserEvent
     */
    public function beforeSend($event)
    {
        $response = $event->sender;
        
        $path = 'v1/user/login';
        if ((int) $response->statusCode === 401 && (Yii::$app->request->url !== $path)) {
            $response->data = [
                'success' => $response->isSuccessful,
                'status_code' => 1032,
                'status_message' => 'Invalid access token'
            ];
            $response->statusCode = 200;
            return;
        }
        
        if ((int) $response->statusCode === 404) {
            $response->data = [
                'success' => $response->isSuccessful,
                "status_code" => 404,
                'status_message' => 'Requested page not found'
            ];
            return;
        }

        $exception = Yii::$app->errorHandler->exception;
        $moduleId = Yii::$app->controller->module->id;
        $response = $event->sender;
        $responseData = $response->data;

        if ($exception || $moduleId === 'debug') {
            return true;
        }
        if (is_array($responseData) && isset($responseData['error'])) {
            $data['status_code'] = $responseData['error']['code'] ?? null;
            $data['status_message'] = $responseData['error']['message'] ?? null;
            $data['status_value'] = $responseData['error']['value'] ?? null;
        } else {
            $data = $responseData;
        }

        if ($response->isSuccessful) {
            if (is_array($data) && !empty($data['pagination'])) {
                $dataReturned = [
                    'data' => $data['reviews']
                ];
                $data = ['success' => $response->isSuccessful] + $data['pagination'] + $dataReturned;
                $response->data = $data;
            } elseif (is_array($data) && isset($data['meta'])) {
                $response->data = ['success' => $response->isSuccessful] + $data['meta'];
                $response->data['data'] = $data['data'];
            } else {
                $response->data = [
                    'success' => $response->isSuccessful,
                    'data' => $data
                ];
            }
        } else {
            $data = ['success' => $response->isSuccessful] + $data;
            $response->data = $data;
        }
        Yii::info('API Response', 'api');
        Yii::info(\yii\helpers\Json::encode($response->data), 'api');
        $response->statusCode = 200;
    }
}
