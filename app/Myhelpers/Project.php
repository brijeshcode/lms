<?php

use Illuminate\Support\Facades\Storage;

	if (! function_exists('uploadFile')) {
		// return file path
		function uploadFile($file, $folder = 'packages'){
			$now = now();
	        $imageName = time().'.'.$file->extension();
	        $location = 'docs'. DIRECTORY_SEPARATOR .$folder.DIRECTORY_SEPARATOR . $now->year;

	        return Storage::url($file->storeAs($location, $imageName, 'public'));
		}
	}

	if (! function_exists('deleteFile')) {
		function deleteFile($filePath, $deletePermanent = false)
	    {
	    	if (! $deletePermanent) return trashedFile($filePath);

	        // replace storage with public
	       	$filePath = str_replace('storage'.DIRECTORY_SEPARATOR.'docs', 'public'.DIRECTORY_SEPARATOR.'docs', $filePath);
            Storage::delete($filePath);
	    }
	}

	if (! function_exists('trashedFile')) {
		// move file to trased file
		function trashedFile($filePath)
	    {
	       	$filePath = str_replace('storage'.DIRECTORY_SEPARATOR.'docs', 'public'.DIRECTORY_SEPARATOR.'docs', $filePath);
	        if(!Storage::exists($filePath)){
                return ''; // file not exist
            }

            $fileName = pathinfo($filePath, PATHINFO_FILENAME);
	    	$ext = pathinfo($filePath, PATHINFO_EXTENSION);
	    	$now = now();
	        $trashFileName = $fileName . '_Deleted_' .(date('d-M-Y')).'.'.$ext;

	        $trashLocation = 'docs'.DIRECTORY_SEPARATOR.'Trashed'.DIRECTORY_SEPARATOR. $now->year. DIRECTORY_SEPARATOR .$trashFileName;
	        Storage::move($filePath, $trashLocation, 'public');
	    }
	}

	if (! function_exists('emptyTrash')) {
		function emptyTrash(){
			// todo : empty trash if it become very large
			/*$tenantName = app('currentTenant')->name;
	        $trashDir = 'storage/docs/'. $tenantName .'/Trashed/';
			$currentDirectory = scandir($trashDir);
			dd($currentDirectory);*/
		}
	}
?>