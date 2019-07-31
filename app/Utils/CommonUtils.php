<?php
namespace App\Utils;
use App\Http\Services\UploadService;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Diff\LongestCommonSubsequenceTest;
use Symfony\Component\Console\Style\OutputStyle;

class CommonUtils
{

    /**
     * @param $excelFile_name
     * @param $insertId
     * @param $field
     * @param int $type
     */
    function setExcelDataToTable($excelFile_name, $insertId, $field, $type = ConstantUtils::SOURCE_UPLOAD){
        $excelFileData = $this->phpExcelFunc($excelFile_name,$type);
        $res = DB::update("update tb_area_config set $field='".$excelFileData."' where id=?", [$insertId]);
        $excel_file_path = $this->getUploadedBasePath($type);
        $this->removeattachment($excel_file_path.$excelFile_name);  //删除临时Excel上传文件
    }

    // 删除附件函数
    public function removeAttachment($attachArr) {
        @chmod($attachArr, 0777);
        @unlink($attachArr);
    }

    /**
     * 获取文件上传的基本路径
     * @param $type
     * @return string
     */
    public function getUploadedBasePath($type)
    {
        switch ($type)
        {
            case ConstantUtils::MODULE_UPLOAD:
                $basePath = base_path().'upload1';
                break;
            default:
                $basePath = dirname(base_path(), 2).'/data';
                break;
        }
        return $basePath;
    }


    /**
     * @param $excel_name
     * @param int $type
     * @return false|string
     */
    function phpExcelFunc( $excel_name, $type = ConstantUtils::WORD_UPLOAD){
        require_once(dirname(base_path()).'/lib/PHPExcel.php');
        $excel_file_path = config('path.excel_dir');
        $excel_name = $excel_file_path.$excel_name;
        /**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if(!$PHPReader->canRead($excel_name)){
            $PHPReader = new \PHPExcel_Reader_Excel5();
            $PHPReader->canRead($excel_name);
        }
        $PHPExcel = $PHPReader->load($excel_name);
        /**读取excel文件中的第一个工作表*/
        $currentSheet = $PHPExcel->getSheet(0);
        /**取得最大的列号*/
        $allColumn = $currentSheet->getHighestColumn();
        /**取得一共有多少行*/
        $allRow = $currentSheet->getHighestRow();
        /**从第二行开始输出，因为excel表中第一行为列名*/
        $excel_data = [];
        if($type== ConstantUtils::WORD_UPLOAD) {  //关键词读取A列
            for($currentRow = 1;$currentRow <= $allRow;$currentRow++){           //从第一行开始
                $file_row = array();
                $res = $currentSheet->getCellByColumnAndRow(ord('A') - 65,$currentRow)->getValue();
                if(!$res) continue;
                /**从第A列开始输出*/
                for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
                    $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
                    if($currentColumn == 'A') {
                        $file_row['keyword'] = $val;
                    }
                }
                $excel_data[] = $file_row;
            }
            return json_encode($excel_data, JSON_UNESCAPED_UNICODE);
        }
        else if ($type == ConstantUtils::SOURCE_RATIO_UPLOAD)
        {
            for($currentRow = 1;$currentRow <= $allRow;$currentRow++){           //从第二行开始
                $file_row = array();
                $res = $currentSheet->getCellByColumnAndRow(ord('A') - 65,$currentRow)->getValue();
                if(!$res) continue;
                /**从第A列开始输出*/
                for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
                    $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
                    if($currentColumn == 'A') {
                        $file_row['url'] = $val;
                    } elseif($currentColumn == 'B'){
                        $dVal = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getFormattedValue();  //计算值(公式) 并非读取原值
                        $file_row['ratio'] = $dVal;
                    }
                }
                $excel_data[] = $file_row;
            }
            return json_encode($excel_data, JSON_UNESCAPED_UNICODE);
        } else if ($type == ConstantUtils::UA_UPLOAD) {
            // ua 文件上传
            for($currentRow = 2;$currentRow <= $allRow;$currentRow++){          //从第二行开始
                $file_row = array();
                $res = $currentSheet->getCellByColumnAndRow(ord('B') - 65,$currentRow)->getValue();  //B列空跳过
                if(!$res) continue;
                /**从第A列开始输出*/
                for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
                    $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
                    if ($currentColumn == 'A') {
                        $file_row['device_type'] = ConstantUtils::DEVICE_TYPES[$val];
                    } else if($currentColumn == 'B'){
                        $file_row['browser'] = ConstantUtils::BROWSER_TYPES[$val];
                    }else if($currentColumn == 'C'){
                        $Dval = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getFormattedValue();  //计算值(公式) 并非读取原值
                        $file_row['visit_ratio'] = $Dval;
                    }
                }
                $excel_data[] = $file_row;
            }
        } else{
            for($currentRow = 2;$currentRow <= $allRow;$currentRow++){          //从第二行开始
                $file_row = array();
                $res = $currentSheet->getCellByColumnAndRow(ord('B') - 65,$currentRow)->getValue();  //B列空跳过
                if(!$res) continue;
                /**从第A列开始输出*/
                for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
                    $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
                    if($currentColumn == 'B'){
                        $file_row['url'] = $val;
                    }elseif($currentColumn == 'D'){
                        $Dval = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getFormattedValue();  //计算值(公式) 并非读取原值
                        $file_row['ratio'] = $Dval;
                    }elseif($currentColumn == 'E'){
                        $Eval = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getFormattedValue();
                        $file_row['num'] = $Eval;
                    }
                }
                $excel_data[] = $file_row;
            }
            return \json_encode($excel_data);
        }
    }

    public static function urlAddCommaTransform($taskUrlContent)
    {
        $contentArr = explode(PHP_EOL,$taskUrlContent);
        $contentArr = array_map('trim', $contentArr);
        $content = join(',', $contentArr);
        return $content;
    }

    static function strInsert($str,$i,$substr) { //方法二：substr函数进行截取
        $start = substr($str,0,$i);
        $end = substr($str,$i);
        $str = ($start . $substr . $end);
        return $str;
    }


    public static function urlAddEolTransform($taskUrlContent)
    {
        $contentArr = explode(',',$taskUrlContent);
        $contentArr = array_map('trim', $contentArr);
        $content = join(PHP_EOL, $contentArr);
        return $content;
    }

    // 给定一个数值序列和一个比率, [1, 2, 3] [50, 30, 20](百分值), 返回一个数值的序列，其中元素的值的概率符合百分比.
    // ratio 中的值不允许出现小数.
    public static function getValueByRatio($values, $ratios)
    {
        $sum = array_sum($ratios);
        if ($sum!=100)
        {
            return false;
        }
        if (count($values) != count($ratios))
        {
            return false;
        }
        $res = [];
        foreach ($values as $k=>$val)
        {
            $newArr = array_fill(0, $ratios[$k], $val);
            foreach ($newArr as $item) {
                $res[] = $item;
            }
        }
        $k = array_rand($res, 1);
        return $res[$k];
    }
}