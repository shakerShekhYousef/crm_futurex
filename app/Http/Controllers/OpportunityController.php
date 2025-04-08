<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use Illuminate\Http\Request;
use App\Models\CandidateClient;
use App\Models\Opportunity;

class OpportunityController extends Controller
{


    public function index($slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $candidateclients = CandidateClient::with('opportunities')->where('workspace',$currentWorkspace->id)->get();

        return view('opportunities.index', [
            'currentWorkspace' => $currentWorkspace,
            'candidateclients' => $candidateclients
        ]);
    }

    public function show($slug, $candidateClientid)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

//        if ($currentWorkspace->creater->id !== \Auth::user()->id) {
//            return redirect()->route('home');
//        }
        $all_opportunities = Opportunity::where('candidate_client_id', $candidateClientid)->get();

        return response()->json($all_opportunities);

    }

    public function Store(Request $request, $slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $validator = \Validator::make($request->all(), [
            'candidate_client_id' => 'required|exists:candidate_clients,id',
            'follow_up_date' => 'required|date',
            'status' => 'required|string',
            'contact_method' => 'required|in:email,call,whatsapp,other,phone',
            'current_notes' => 'required|string',
            'future_notes' => 'required|string',
            'next_follow_up_date' => 'required|date',
            'other_status' => 'required_if:status,other|nullable|string|max:50',
            'other_contact_method' => 'required_if:contact_method,other|nullable|string|max:50'
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $status = $request->status === 'other'
            ? $request->other_status
            : $request->status;

        $contact_method = $request->contact_method === 'other'
            ? $request->other_contact_method
            : $request->contact_method;

        $opportunity = [
            'candidate_client_id' => $request->candidate_client_id,
            'follow_up_date' => $request->follow_up_date,
            'status' => $status,
            'contact_method' => $contact_method,
            'current_notes' => $request->current_notes,
            'future_notes' => $request->future_notes,
            'next_follow_up_date' => $request->next_follow_up_date,
        ];

        Opportunity::create($opportunity);

        return redirect()->back()->with('success', __('Opportunity added Successfully!'));
    }
    public function update(Request $request,$slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'company_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $contact_method = $request->contact_method === 'other'
            ? $request->custom_contact_method
            : $request->contact_method;

        $candidateclient = CandidateClient::findOrFail($request->id);


        $latestOpportunity = $candidateclient->opportunities()->latest()->first();
        if ($latestOpportunity) {
            $latestOpportunity->update([
                'follow_up_date' => $request->follow_up_date,
                'contact_method' => $contact_method,
            ]);
        }


        $candidateclient->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'company_name' => $request->company_name,
        ]);

        return redirect()->back()->with('success', __('Candidate Updated Successfully!'));
    }

    public function downloadExample($workspace)
    {
        // Ensure the file exists in public/example_files directory
        $filePath = public_path('example_files/opportunity_example.xlsx');

        if(!file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath, 'opportunity_template.xlsx');
    }

}
