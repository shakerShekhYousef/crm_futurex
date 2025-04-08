<?php

namespace App\Http\Controllers;

use App\Imports\CandidateClientsImport;
use App\Models\CandidateClient;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;


class CandidateClientController extends Controller
{


    public function uploadExcelFile(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xls,xlsx,csv|max:256000'
            ]);

            $file = $request->file('file');
            $currentWorkspace = Utility::getWorkspaceBySlug($request->workspace);
            $import = new CandidateClientsImport($currentWorkspace->id);
            Excel::import($import, $file);

            return response()->json([
                'message' => __('File uploaded successfully'),
                'stats' => [
                    'imported' => $import->getImportedCount(),
                    'skipped' => $import->getSkippedCount()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => __('Error during import'),
                'errors' => [
                    'file' => [$this->getErrorMessage($e)]
                ]
            ], 422);
        }

    }

    private function getErrorMessage(\Exception $e): string
    {
        if ($e instanceof \Illuminate\Http\Exceptions\PostTooLargeException) {
            return __('File size exceeds maximum allowed limit');
        }

        if ($e instanceof \Maatwebsite\Excel\Exceptions\NoTypeDetectedException) {
            return __('Invalid file type');
        }

        return __('Invalid file format or structure');
    }



    public function create(Request $request,$slug){

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $validator = \Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'company_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $candidateclient =  new CandidateClient();
        $candidateclient->name = $request->name;
        $candidateclient->email = $request->email;
        $candidateclient->phone = $request->phone;
        $candidateclient->company_name = $request->company_name;
        $candidateclient->workspace = $currentWorkspace->id;

        $candidateclient->save();


        return redirect()->back()->with('success', __('Candidate added Successfully!'));

    }



}
