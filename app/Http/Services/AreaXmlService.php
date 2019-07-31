<?php
namespace App\Http\Services;

use App\Repository\Admin\AreaTaskRepository;
use App\Utils\RecursiveFilterFileIterator;
use RecursiveIteratorIterator;

class AreaXmlService
{
    protected $path;
    public function __construct()
    {
        $this->path = rtrim(dirname(base_path(), 2), '/').'/log/_nsm_xml_log';
    }

    public function getAreaXml($perPage = 50, $page = 1, array $condition = [])
    {
        $taskId = isset($condition['taskId'])? (int) $condition['taskId']:0;
        $dir = new \RecursiveDirectoryIterator($this->path);
        $itr = new \RecursiveIteratorIterator(new RecursiveFilterFileIterator($dir, $taskId),
            RecursiveIteratorIterator::SELF_FIRST);
        $itr = new \LimitIterator($itr, ($page-1)*$perPage, $page);
        $res = [];
        $taskIdArr = [];
        $i = 0;
        foreach ($itr as $k=>$file)
        {
            $i++;
            $filePath = $file->getPathname();
            $fileName = $file->getFilename();
            $fileNameArr = explode('_', $fileName);
            $taskIdStr   = end($fileNameArr);
            $taskId = strstr($taskIdStr, '.txt', 1);
            $contents = file_get_contents($filePath);
            $xml = json_decode($contents, true);
            $cfgHash = $xml['hash'];
            $content  = $xml['content'];
            $content = htmlentities($content,ENT_QUOTES,"UTF-8");
            $content = str_replace("\n", "<br/>",$content);
            $mtime = date('H:i:s',filemtime($filePath));
            $taskIdArr[] = $taskId;
            $res[$taskId] = [
                'taskId' => $taskId,
                'mtime'  => $mtime,
                'cfg_hash' => $cfgHash,
                'content'  => $content,
                'no'       => ((int)$page-1)*(int)$perPage + $i
            ];
        }
        $taskNameMap = AreaTaskRepository::getTaskName($taskIdArr);
        foreach ($res as $taskId => $row)
        {
            $res[$taskId]['taskName'] =
                isset($taskNameMap[$taskId])? $taskNameMap[$taskId]: '';
        }
        return $res;
    }

    public function countAreaXml($taskId)
    {
        $path = realpath($this->path);
        $cmd = "cd  $path && ls |grep '_$taskId.txt'|wc -l";
        return (int)shell_exec($cmd);
    }
}