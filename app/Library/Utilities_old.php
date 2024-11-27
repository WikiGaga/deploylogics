<?php
/**
 * Created by PhpStorm.
 * User: M.Imran
 * Date: 05/13/2016
 * Time: 12:25 PM
 */

namespace App\Library;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Image;

class Utilities_old
{
	
	/**
	 * Generate invoice number to call.
	 *
	 * @param $pattern
	 */
	public static function generateInvoice()
	{
		$invoiceString = Date('Ymd').'-'.rand(10000, 99999);//strtoupper(Str::quickRandom(12));
		
		$checkInvoice = 0;//Enquiry::withTrashed()->where('job_no', '=', $invoiceString)->count();
		
		if ($checkInvoice > 0) {
			return Utilities::generateInvoice();
		}
		
		return $invoiceString;
	}

    public static function decimalToHexadecimal($decimal)
    {
        $decimal = strtoupper(dechex($decimal));
        return $decimal;
    }

    public static function isBase64Encoded($str)
    {
        try
        {
            $decoded = base64_decode($str, true);

            if ( base64_encode($decoded) === $str ) {
                return true;
            }
            else {
                return false;
            }
        }
        catch(Exception $e)
        {
            // If exception is caught, then it is not a base64 encoded string
            return false;
        }

    }

    public static function base64ToImg( $base64String, $directory, $output_file= "", $type="jpg", $isThumbnail = TRUE, $thumbnailsAttribute = [])
    {
        if(self::isBase64Encoded($base64String)){
            if($output_file == "") {
                $output_file = uniqid( '', TRUE ) . '_' . time() . '_' . rand(10000,99999);
            }
            $output_file = $output_file.'.'.$type;
            $directory = public_path($directory);
            if (!is_dir($directory))
            {
	            File::makeDirectory($directory, 0777, true, true);
            }
            $directory = $directory.'/';
            $ifp = fopen( $directory.$output_file, "wb" );
            fwrite( $ifp, base64_decode( $base64String) );
            fclose( $ifp );
            ////////////////////////////////////////
            if($isThumbnail == TRUE){
                $prefix = "";
                $thumbnail_dir = $directory."thumbnails/";
                if (!is_dir($thumbnail_dir))
                {
	                File::makeDirectory($thumbnail_dir, 0777, true, true);
                }
                $width = (isset($thumbnailsAttribute['width']) && !empty($thumbnailsAttribute['width']))?$thumbnailsAttribute['width']:env('THUMBNAILS_WIDTH', 250);
                $height = (isset($thumbnailsAttribute['height']) && !empty($thumbnailsAttribute['height']))?$thumbnailsAttribute['height']:env('THUMBNAILS_HEIGHT', 180);
                Image::make($directory.$output_file)->fit($width, $height)->save($thumbnail_dir.$output_file);
                //Image::make('G:\xampp\htdocs\patchup\public\uploads\user\profile\575086844e730_1464895108_68658.jpg')->resize(300, 200)->insert($thumbnail_dir.$output_file);
                //copy($directory.$output_file,$thumbnail_dir.$prefix.$output_file);
                //resizeImg($thumbnail_dir.$prefix.$output_file,145,145,"fix");
            }
        }else{
            $output_file = "";
        }
        return( $output_file );
    }

    public static function uploadImage( $file, $directory, $output_file= "", $original_extension = 'jpg', $extension =  'jpg', $isThumbnail = TRUE, $thumbnailsAttribute = [], $compression = 70 )
    {
    	try{
		    $output_file = "";
		    if($output_file == "") {
			    $output_file = uniqid( '', TRUE ) . '_' . time() . '_' . rand(10000,99999);
		    }
		    $output_file = $output_file.'.'.$file->getClientOriginalExtension();


		    File::makeDirectory(public_path($directory), 0755, true, true);
		    
		    /*$directory = $directory.'/';
		    $file->move($directory, $output_file);*/
		    
		    $imgOpen = Image::make($file);
		    //Compress Image
		    if($original_extension == 'jpg'){
			    $imgOpen->encode('jpg', $compression);
		    }else{
			    $imgOpen->encode('jpg', $compression);
		    }
		    $imgOpen->save(public_path($directory . $output_file));
		    if($isThumbnail == TRUE){
			    $prefix = "";
			    $thumbnail_dir = $directory."thumbnails/";
			    File::makeDirectory(public_path($thumbnail_dir), 0755, true, true);
			    
			    $width = (isset($thumbnailsAttribute['width']) && !empty($thumbnailsAttribute['width']))?$thumbnailsAttribute['width']:env('THUMBNAILS_WIDTH', 250);
			    $height = (isset($thumbnailsAttribute['height']) && !empty($thumbnailsAttribute['height']))?$thumbnailsAttribute['height']:env('THUMBNAILS_HEIGHT', 180);
			    $imgOpen->resize($width, $height, function($constraint) {
				    $constraint->aspectRatio();
			    })->save(public_path(public_path($thumbnail_dir).$output_file));
		    }
	    }catch (\Exception $e){
    		return $e->getMessage();
	    }
        return( $output_file );
    }
    
    public static function uploadImageMedias( $file, $baseDir, $output_file = "", $original_extension = 'jpg', $extension =  'jpg', $thumbnailsAttribute = [], $compression = 70)
    {
        $return = [];
        try {
            if($output_file == "") {
                $output_file = uniqid( '', TRUE ) . '_' . time() . '_' . rand(10000,99999);
            }
    
            $original_output_file = $output_file.'.'.$original_extension;
            $output_file = $output_file.'.'.$extension;
            
            $originalDir = $baseDir.'original/';
            $thumbnailDir = $baseDir."thumbnails/";
            $thumbnailDir50x50 = $baseDir."50x50/";
            $thumbnailDir100x100 = $baseDir."100x100/";
            $thumbnailDir150x150 = $baseDir."150x150/";
	        $thumbnailArray = [
	        	'50x50', '100x100', '150x150'
	        ];
            File::makeDirectory($originalDir, 0777, true, true);
            File::makeDirectory($thumbnailDir, 0777, true, true);
	        foreach ( $thumbnailArray as $item ) {
		        File::makeDirectory($baseDir.$item.'/', 0777, true, true);
	        }
            $width = (isset($thumbnailsAttribute['width']) && !empty($thumbnailsAttribute['width']))?$thumbnailsAttribute['width']:env('THUMBNAILS_WIDTH', 250);
            $height = (isset($thumbnailsAttribute['height']) && !empty($thumbnailsAttribute['height']))?$thumbnailsAttribute['height']:env('THUMBNAILS_HEIGHT', 180);
    
    
    
            $imgOpen = Image::make($file);
    
            $originalSize = $imgOpen->filesize();
            $originalWidth = $imgOpen->width();
            $originalHeight = $imgOpen->height();
            $imgOpen->save(public_path($originalDir.$original_output_file));
    
            //Compress Image
            if($original_extension == 'jpg'){
                $imgOpen->encode('jpg', $compression)->save(public_path($baseDir . $output_file));
            }else{
                $imgOpen->encode('jpg', $compression)->save(public_path($baseDir . $output_file));
            }
    
            $optimizeSize = $imgOpen->filesize();
            $optimizeWidth = $imgOpen->width();
            $optimizeHeight = $imgOpen->height();
    
    
            //Resize Image
            $imgOpen->resize($width, $height, function($constraint) {
                $constraint->aspectRatio();
            })->save(public_path($thumbnailDir.$output_file));
            $thumbnailSize = $imgOpen->filesize();
            $thumbnailWidth = $imgOpen->width();
            $thumbnailHeight = $imgOpen->height();
	
	        $imgOpen->resize(150, 150, function($constraint) {
		        $constraint->aspectRatio();
	        })->save(public_path($thumbnailDir150x150.$output_file));
	        $thumbnailSize150x150 = $imgOpen->filesize();
	        $thumbnailWidth150x150 = $imgOpen->width();
	        $thumbnailHeight150x150 = $imgOpen->height();
	
	
	        $imgOpen->resize(100, 100, function($constraint) {
		        $constraint->aspectRatio();
	        })->save(public_path($thumbnailDir100x100.$output_file));
	        $thumbnailSize100x100 = $imgOpen->filesize();
	        $thumbnailWidth100x100 = $imgOpen->width();
	        $thumbnailHeight100x100 = $imgOpen->height();
	
	
	        $imgOpen->resize('50', '50', function($constraint) {
		        $constraint->aspectRatio();
	        })->save(public_path($thumbnailDir50x50.$output_file));
	        $thumbnailSize50x50 = $imgOpen->filesize();
	        $thumbnailWidth50x50 = $imgOpen->width();
	        $thumbnailHeight50x50 = $imgOpen->height();
	
	
	        $return = [
                    'name'      => $output_file,
                    'extension' => 'jpg',
                    'path'      => $baseDir,
                    'main_url'       => [
                            'url'       => $baseDir.$original_output_file,
                            'size'      => $optimizeSize,
                            'width'     => $optimizeWidth,
                            'height'    => $optimizeHeight,
                    ],
                    'thumbnails'       => [
                    	'common'    => [
		                    'url'       => $thumbnailDir.$output_file,
		                    'size'      => $thumbnailSize,
		                    'width'     => $thumbnailWidth,
		                    'height'    => $thumbnailHeight,
	                    ],
                    	'50x50' => [
		                    'url'       => $thumbnailDir50x50.$output_file,
		                    'size'      => $thumbnailSize50x50,
		                    'width'     => $thumbnailWidth50x50,
		                    'height'    => $thumbnailHeight50x50,
	                    ],
	                    '100x100' => [
		                    'url'       => $thumbnailDir100x100.$output_file,
		                    'size'      => $thumbnailSize100x100,
		                    'width'     => $thumbnailWidth100x100,
		                    'height'    => $thumbnailHeight100x100,
	                    ],
	                    "150x150" => [
		                    'url'       => $thumbnailDir150x150.$output_file,
		                    'size'      => $thumbnailSize150x150,
		                    'width'     => $thumbnailWidth150x150,
		                    'height'    => $thumbnailHeight150x150,
	                    ]
                    ],
                    'original'       => [
                            'url'       => $originalDir.$output_file,
                            'size'      => $originalSize,
                            'width'     => $originalWidth,
                            'height'    => $originalHeight,
                    ]
            ];
            /*
              [
                    'name'      => 'chech-up.png',
                    'extension' => 'png',
                    'type'      => 'image/png',
                    'path'      => 'uploads/services/',
                    'main_url'       => [
                            'url'       => 'uploads/services/chech-up.png',
                            'size'      => '34364',
                            'width'     => '150',
                            'height'    => '150',
                    ],
                    'thumbnails'       => [
                    	'50x50' => [
		                    'url'       =>  'uploads/services/50x50/chech-up.png',
		                    'size'      => '1500',
		                    'width'     => '50',
		                    'height'    => '50',
	                    ],
	                    '100x100' => [
		                    'url'       =>  'uploads/services/100x100/chech-up.png',
		                    'size'      => '23545',
		                    'width'     => '150',
		                    'height'    => '100',
	                    ],
	                    "150x150" => [
		                    'url'       =>  'uploads/services/150x150/chech-up.png',
		                    'size'      => '34364',
		                    'width'     => '150',
		                    'height'    => '150',
	                    ]
                    ],
                    'original'       => [
                            'url'       =>  'uploads/services/original/chech-up.png',
                            'size'      => '34364',
                            'width'     => '150',
                            'height'    => '150',
                    ]
            ]
             */
    
        }catch (\Exception $e){
            return $e->getMessage();
        }
        return $return;
    }

    public static function doPutCurl($url, $fields) {
        $fields = (is_array($fields)) ? http_build_query($fields) : $fields;

        if ($ch = curl_init($url)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($fields)));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_exec($ch);

            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (!curl_errno($ch)) {
                $info = curl_getinfo($ch);
            }

            curl_close($ch);
            //return $info;
            //curl_close($ch);

            return (int) $status;
        } else {
            return false;
        }
    }

	public static function sendUserEmail($to, $to_name,$subject, $heading, $bodyMessage, $from = null, $from_name = null){
		Mail::send('emails.user_registration', [
			'heading' => $heading,
			'bodyMessage' => $bodyMessage
		], function ($m) use ($to, $to_name, $subject, $from, $from_name) {
			if($from != null)
				$m->from($from, $from_name);
			$m->to($to, $to_name)->subject($subject);
		});
	}
    
    /**
     * @param $filePath
     * @return bool
     */
    public static function isFileExists($filePath)
    {
        return is_file($filePath) && file_exists($filePath);
    }
	
	public static function breakTime($start_time,$end_time,$time_duration, $format="H:i:s"){
		////////////////////////////////////////////////////////////////////////////
		if($start_time!=''){
			$timeArray = explode (":", $start_time );
			$start_time = $timeArray[0].':'.$timeArray[1].':00';
		}
		if($end_time!=''){
			$timeArray1 = explode (":", $end_time );
			$end_time = $timeArray1[0].':'.$timeArray1[1].':00';
		}
		$array_of_time = array ();
		$start_time    = strtotime ($start_time);
		$end_time      = strtotime ($end_time);
		
		while ($start_time <= $end_time)
		{
			$array_of_time[] = date ($format, $start_time);
			$start_time += $time_duration;
		}
		
		return $array_of_time;
		////////////////////////////////////////////////////////////////////////////
	}
}
