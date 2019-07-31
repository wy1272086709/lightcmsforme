<?php


namespace App\Repository\Admin;


use App\Model\Admin\BatchSetSource;
use App\Model\Admin\Model\CustomSource;
use App\Model\Admin\TaskUpload;
use App\Model\Admin\TaskWord;
use App\Utils\ConstantUtils;

class TaskWordRepository
{
    /**
     * @param $taskId
     * @param $data
     * @return integer
     */
    public static function insertTaskWord($taskId, $data)
    {
        $wordStyle   = isset($data['word_style']) ? $data['word_style']: '';
        $wordContent = isset($data['word_content']) ? $data['word_content']: '';
        if ($wordStyle == ConstantUtils::WORD_TEXT_EXPORT_STYLE)
        {
            return TaskWord::query()->insertGetId([
                'word_content' => $wordContent,
                'task_id'      => $taskId
            ]);
        }
        else
        {
            return TaskUploadRepository::insertUploadData($taskId, $wordContent, ConstantUtils::WORD_UPLOAD);
        }
    }

    /**
     * @param $taskId
     * @param $data
     * @return integer
     */
    public static function editTaskWord($taskId, $data)
    {
        $wordStyle   = isset($data['word_style']) ? $data['word_style']: '';
        $wordContent = isset($data['word_content']) ? $data['word_content']: '';
        if ($wordStyle == ConstantUtils::WORD_TEXT_EXPORT_STYLE)
        {
            return TaskWord::query()->where(
                'task_id'     , $taskId
            )->update([ 'word_content' => $wordContent ]);
        }
        else
        {
            return TaskUploadRepository::updateUploadData($taskId, $wordContent, ConstantUtils::WORD_UPLOAD);
        }
    }

    /**
     * @param $taskId
     * @param $data
     * @return integer
     */
    public static function saveTaskWord($taskId, $data)
    {
        $wordStyle   = isset($data['word_style']) ? $data['word_style']: '';
        if (self::existTaskWord($taskId, $wordStyle)) {
            return self::editTaskWord($taskId, $data);
        } else {
            return self::insertTaskWord($taskId, $data);
        }
    }

    /**
     * @param $taskId
     * @param $taskWordStyle
     * @return integer
     */
    public static function existTaskWord($taskId, $taskWordStyle)
    {
        if ($taskWordStyle == ConstantUtils::WORD_TEXT_EXPORT_STYLE)
        {
            return TaskWord::query()->where('task_id', $taskId)
                ->exists();
        }
        else
        {
            return TaskUpload::query()->where('task_id', $taskId)
                ->where('upload_type', ConstantUtils::WORD_FILE_UPLOAD_STYLE)
                ->exists();
        }
    }
}