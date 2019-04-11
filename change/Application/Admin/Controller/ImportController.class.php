<?php
namespace Admin\Controller;
use Think\Controller;
use Libs\Upload;

class ImportController extends Controller {
	public function importPrice(){
		
		//写数据
		// if(isset($_POST['post'])){
			// $post=$_POST['post'];
			// $code=$_POST['code'];
			// $row=$_POST['row'];
		  
			// if($code<$row){
				// $curData=$post[$code];
				// $code++;
				// $model=$curData[0];
				// $where=array();
				// $where['model_name']=$model;
				// $res=M("Specification")->where($where)->save(array("inventory"=>$curData[1]));
				// if(($res!==false)&&($res>0)){
					// exit(json_encode(array("code"=>$code,"msg"=>"导入型号：".$model."的库存数据成功！","post"=>$post,"row"=>$row)));
				// }else{
					// exit(json_encode(array("code"=>$code,"msg"=>"导入型号：".$model."的库存数据失败","post"=>$post,"row"=>$row)));
				// }
			// }else{
				// $code++;
				// exit(json_encode(array("code"=>$code,"msg"=>"数据导入完成")));
			// }
		// }
	  
		//读数据
		if(!isset($_POST['post'])){
			// 引入文件
			Vendor('PHPExcel.PHPExcel');
			$fileinfo=Upload::upload($_FILES['file'],"import_data",array("xls","xlsx"),5);
			$type=$fileinfo['ext'];
			$filepath="./Public/".$fileinfo['savepath'].$fileinfo['savename'];
			// 创建读取器
			if($type=="xls"||$type=="xlsx"){
				$reader = \PHPExcel_IOFactory::createReader('Excel2007'); // 读取 excel 文档
				if(!$reader->canRead($filepath)) {
					$reader = \PHPExcel_IOFactory::createReader('Excel5');
					if(!$reader->canRead($filepath)){
						$this->error("不能读取文件");
						exit;
					}
				}
			}else{
				$this->error("文件类型不支持");
				exit;
			}
		   
			$PHPExcel = $reader->load($filepath); // 文档名称
			$objWorksheet = $PHPExcel->getActiveSheet();
			$highestRow = $objWorksheet->getHighestRow(); // 取得总行数
		   
			$res=array();
			for ($row = 2; $row <= $highestRow; $row++) {
				//规格
				$specId= $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
				//价格
				$price= $objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
				//等级
				$gradeId= $objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
				
				if(!empty($specId)&&!empty($gradeId)){
					//$data=array();
					//$data[]=$specId;
					//$data[]=empty($price)?0:$price;
					//$data[]=$gradeId;
					//$res[]=$data;
					
					M("Grade_product")->where(array("grade_id"=>$gradeId,"specification_id"=>$specId))->save(array("rmb"=>empty($price)?0:$price));
				}
			}
			//exit(json_encode(array("code"=>0,"msg"=>"数据读取完成，准备更新数据","post"=>$res,"row"=>count($res))));
			exit(json_encode(array("code"=>1379,"msg"=>"数据导入完成")));
		}
	}
   
   
	//导入库存操作
	public function importInventory(){
		if(isset($_POST['post'])){
			
			$key=$_POST['post'];//获取缓存key值
			
			$post=S($key);
			
			$row=$_POST['row'];
			$success=$_POST['success'];
		  
			if($row>0){
				$curData=array_shift($post);
				S($key,$post,3600*12);//缓存
				$model=trim($curData["prd_no"]);
				if(!empty($model)){
					$where=array();
					$where['model_name']=$model;
					$res=M("Specification")->where($where)->save(array("inventory"=>($curData["qty"]-$curData["qty_lrn"])));
					if(($res!==false)&&($res>0)){
						$success++;
						exit(json_encode(array("msg"=>"导入型号：".$model."的库存数据成功！","post"=>$key,"row"=>count($post),"success"=>$success)));
					}else{
						if($res===0){
							$count=M("Specification")->where($where)->count();
							if($count>0){
								exit(json_encode(array("msg"=>"导入型号：".$model."的库存数据成功！","post"=>$key,"row"=>count($post),"success"=>$success)));
							}else{
								exit(json_encode(array("msg"=>"失败！数据库没有对应型号：".$model,"post"=>$key,"row"=>count($post),"success"=>$success)));
							}
						}else{
							exit(json_encode(array("msg"=>"导入型号：".$model."的库存数据失败！","post"=>$key,"row"=>count($post),"success"=>$success)));
						}
					}
				}else{
					exit(json_encode(array("msg"=>"失败！源数据库型号是空值","post"=>$key,"row"=>count($post),"success"=>$success)));
				}
			}else{
				exit(json_encode(array("msg"=>"数据导入完成")));
			}
		}
		if(!isset($_POST['post'])){
			$query="select * from STK.dbo.KCSL";
			$res=M()->db(1379,"DB_CONFIG1")->query($query);
			
			//生成随机数字
			$key="inventory_".substr(md5(time()),6,12);
			
			//缓存数据12小时,解决php的post数量限制
			S($key,$res,3600*12);
			
			exit(json_encode(array("msg"=>"数据读取完成，准备更新数据","post"=>$key,"row"=>count($res),"success"=>0)));
		}
	}
	
	//更新数据库时间
	
	public function updateTime(){
		
		if(IS_POST){
			
			$success=I("post.success");
			
			$where=array(
				"config_name"=>"inventory_time"
			);
			
			$data=array(
				"time"=>time(),
				"status"=>"ok",
				"success"=>$success
			);
			
			$count1=M("Config")->where($where)->count();
			
			if($count1>0){
				
				
				M("Config")->where($where)->save(array("config_value"=>json_encode($data)));
				
			}else{
				
				M("Config")->add(array("config_name"=>"inventory_time","config_value"=>json_encode($data)));
				
			}
			
		}
	}
	
	//导出数据库中的价格
	// public function exportPrice(){
		
		//引入文件
	    // Vendor('PHPExcel.PHPExcel');
	    // $objPHPExcel = new \PHPExcel();
		// $objPHPExcel->setActiveSheetIndex(0)
					// ->setCellValue("A1","型號")
					// ->setCellValue("B1","訂單總數量");
		// foreach($orderDetail as $k => $v){
			// $objPHPExcel->setActiveSheetIndex(0)
					// ->setCellValue("A".($k+2),$v['model_name'])
					// ->setCellValue("B".($k+2),$v['total_number']);
        // }
		// $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
		// $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		// header('Content-Type: application/vnd.ms-excel');
		// header('Content-Disposition: attachment;filename="'.$orderInfo['order_serial_number'].'.xls"');
		// header('Cache-Control: max-age=0');
		// $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		// $objWriter->save('php://output');
		// exit;
	// }
	
}