<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Job;
use App\Models\JobCable;
use App\Models\JobUser;
use App\Models\JobDrawing;
use App\Models\CableType;
use App\Models\CableMaster;
use App\Models\JobTermination;
use App\Models\JobTerminationDetail;
use App\Models\JobCableLocation;
use App\Models\TestParameter;
use App\Models\JobTestResultDetail;
use App\Models\JobFinalCheckSheet;
use App\Models\JobFinalCheckSheetDetails;
use App\Models\FinalCheckSheetQuestionnaire;
use App\Models\JobAreaOfWorkDetail;
use App\Models\JobLocation;
use App\Models\ReportedIssue;
use App\Models\ChecklistMaster;
use App\Models\JobChecklistDetail;

use Response;
use Carbon\Carbon;
use PDF;

class GenerateJobCablePdfCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generatejobcablepdf:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To create a pdf for each cable after job closed.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("Cron starts");
        return $this->getClosedJobCables();
    }


    public function getClosedJobs() 
    {
        $whereArr = [
            'status'           => 2,
            'is_pdf_generated' => 0
        ];
        $closedJobList = Job::with('jobCables')->where($whereArr)->get();
        \Log::info('List Closed Job');
        return $closedJobList;
    }

    public function getClosedJobCables() 
    {
        $listJobs = collect($this->getClosedJobs());

        if(!$listJobs->isEmpty()) {
            $listJobs->each(function ($job, $key) {
                $jobPdfCreated = false;

                foreach ($job->jobCables as $k => $cable) {
                    if(empty($cable->file_name)) {

                        if($this->generatePdf($cable)) {
                            $jobPdfCreated = true;
                        } else {
                            $jobPdfCreated = false;
                        }
                    }
                }
                if($jobPdfCreated) {
                    $job->is_pdf_generated = 1;
                    $job->save();
                }
            });
        }
        \Log::info('get closed jobs cable');
    }

    public function generatePdf($cable) 
    {
        \Log::info($cable->cable_id . ' @@@ pdf in progress.');

        $jobsDir   = \Config::get('constants.uploadPaths.jobs');
        $pdfPath   = \Config::get('constants.uploadPaths.jobCablePdf');
        $jobNumber = $cable->job->job_number;
        $pdfDir    = $jobsDir . $jobNumber.DIRECTORY_SEPARATOR.$pdfPath;

        try {
            $jobCompany = $cable->job->jobCompany;

            $jobNumber  = $cable->job->job_number;
            $cableName  = $cable->cable_id;
            if($cable->custom_id)
            {
                $cableName     = $cableName .'/'. $cable->custom_id;
            }
            if($cable->unqique_code)
            {
                $cableName     = $cableName .'/'. $cable->unqique_code;
            }

            $toLocationName = $cable->jobCableTo->jobLocation->location_name;
            $fromLocationName = $cable->jobCableFrom->jobLocation->location_name;

            $cableLocationName = $toLocationName . '-' . $fromLocationName;

            $companyLogo = asset(\Config::get('constants.static.staticProfileImage'));

            if(isset($jobCompany->company_logo) && !empty($jobCompany->company_logo)) {
                $companyLogoPath = \Config::get('constants.uploadPaths.viewCompanyLogo');

                $companyLogo = public_path($companyLogoPath . $jobCompany->company_logo);
            }

            $fileExtension = \File::extension($companyLogo);
            $companyLogo   = 'data:image/'.$fileExtension.';base64,'.base64_encode(file_get_contents($companyLogo));

            $pdfName = 'cable_information_'.$jobNumber.'_'.$cable->cable_id.'_'.$cableLocationName.'.pdf';

            $terminationWhereToArr = [
                'client_id'   => $cable->job->client_id, 
                'job_id'      => $cable->job_id,
                'cable_id'    => $cable->id,
                'location_id' => $cable->jobCableTo->id,
            ];

            $terminationTo = JobTermination::where($terminationWhereToArr)->first();

            $terminationWhereFromArr = [
                'job_id'      => $cable->job->client_id,
                'cable_id'    => $cable->id,
                'location_id' => $cable->jobCableFrom->id,
            ];

            $terminationFrom = JobTermination::where($terminationWhereFromArr)->first();

            // get checklists for form
            $checklistWhereArr = [
                'client_id' => $cable->job->client_id,
                'user_id'   => $cable->job->user_id,
            ];

            $checklistMaster = ChecklistMaster::where($checklistWhereArr)->get();

            $jobChecklistWhereArr = [
                'job_id'        => $cable->job_id, 
                'job_cable_id'  => $cable->id,
            ];
            $jobChecklistDetails = JobChecklistDetail::where($jobChecklistWhereArr)->get();

            $getTestParameter = TestParameter::where('client_id', $cable->job->client_id)->get();

            $jobTestResultsWhereArr = [
                'job_id'       => $cable->job_id, 
                'job_cable_id' => $cable->id,
            ];

            $jobTestResult = JobTestResultDetail::where($jobTestResultsWhereArr)->get();

            $pdf = PDF::loadView('jobs.pdf-template.job_cable_pdf', compact('companyLogo', 'pdfName', 'cable', 'terminationTo', 'terminationFrom', 'checklistMaster', 'jobChecklistDetails', 'getTestParameter', 'jobTestResult'));

            if(!\File::isDirectory($pdfDir)){
               \File::makeDirectory($pdfDir, 0775, true);
            }
            // If you want to store the generated pdf to the server then you can use the store function
            if($pdf->save($pdfDir.$pdfName)) {
                $cable->file_name = $pdfName;
                $cable->save();
                \Log::info($cable->cable_id . ' @@@ pdf saved.');
                return true;
            } else {
                \Log::info('Error in saving pdf'. $cable->cable_id);
                return false;
            }

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            \Log::info('error in generating pdf');
        }
    }
}
