<?php
namespace App\Utils;

use RecursiveIterator;

class RecursiveFilterFileIterator extends \RecursiveFilterIterator
{
    public $taskId;
    public $iterator;
    public function __construct(RecursiveIterator $iterator, $taskId)
    {
        parent::__construct($iterator);
        $this->iterator = $iterator;
        $this->taskId = $taskId;
    }

    public function accept()
    {
        $file = $this->current();
        if ($file instanceof \SplFileInfo)
        {
            $fileName   = $file->getFilename();
            $pathDirArr = explode('_', $fileName);
            $endStr = end($pathDirArr);
            if (!$this->taskId)
            {
                return strlen($pathDirArr[0]) === 8 &&
                    ends_with($fileName, '.txt') && !in_array($fileName, [ '.', '..' ]);
            }
            return $endStr === $this->taskId.".txt" && strlen($pathDirArr[0]) === 8 &&
                ends_with($fileName, '.txt');
        }
    }


}
