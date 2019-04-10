<?php

namespace Softnio\LaravelInstaller\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Softnio\LaravelInstaller\Helpers\EnvironmentManager;
use Softnio\LaravelInstaller\Events\EnvironmentSaved;
use Validator;
use DB;

class EnvironmentController extends Controller
{
    /**
     * @var EnvironmentManager
     */
    protected $EnvironmentManager;

    /**
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(EnvironmentManager $environmentManager)
    {
        $this->EnvironmentManager = $environmentManager;
    }

    /**
     * Display the Environment menu page.
     *
     * @return \Illuminate\View\View
     */
    public function environmentMenu()
    {
        return view('vendor.installer.environment');
    }

    /**
     * Display the Environment page.
     *
     * @return \Illuminate\View\View
     */
    public function environmentWizard()
    {
        $envConfig = $this->EnvironmentManager->getEnvContent();

        return view('vendor.installer.environment-wizard', compact('envConfig'));
    }

    /**
     * Display the Environment page.
     *
     * @return \Illuminate\View\View
     */
    public function environmentClassic()
    {
        $envConfig = $this->EnvironmentManager->getEnvContent();
        
        try{
            DB::connection()->getPdo();
            $checkConnection = (DB::connection()->getDatabaseName()) ? true : false;
        }catch(Exception $e){
           $checkConnection = false;
        }

        return view('vendor.installer.environment-classic', compact('envConfig', 'checkConnection'));
    }

    /**
     * Processes the newly saved environment configuration (Classic).
     *
     * @param Request $input
     * @param Redirector $redirect
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveClassic(Request $input, Redirector $redirect)
    {
        $message = $this->EnvironmentManager->saveFileClassic($input);
        $results = $this->EnvironmentManager->saveFileWizard($request);
        
        $message = (isset($results['message']) ? $results['message'] : '');
        $response = (isset($results['response']) ? $results['response'] : '');

        if($response == true){
            try {
                \DB::connection()->getPdo();
                if(\DB::connection()->getDatabaseName()){
                    session()->forget('envConfigData');
                    event(new EnvironmentSaved($input));
                    return $redirect->route('LaravelInstaller::environmentClassic')
                        ->with(['message' => $message, 'showInstallButton' => true]);
                }else{
                    return $redirect->route('LaravelInstaller::environmentClassic')
                        ->with(['message' => 'Wrong database connection!']);
                }
            } catch (\Exception $e) {
                return $redirect->route('LaravelInstaller::environmentClassic')
                        ->with(['message' => 'Wrong database connection!']);
            }
        }else{
            return $redirect->route('LaravelInstaller::environmentClassic')
                        ->with(['message' => $message]);
        }
    }

    /**
     * Processes the newly saved environment configuration (Form Wizard).
     *
     * @param Request $request
     * @param Redirector $redirect
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveWizard(Request $request, Redirector $redirect)
    {
        $rules = config('installer.environment.form.rules');
        $messages = [
            'environment_custom.required_if' => trans('installer_messages.environment.wizard.form.name_required'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return view('vendor.installer.environment-wizard', compact('errors', 'envConfig'));
        }
        if(testDatabaseConnection($request->database_hostname, $request->database_username, $request->database_password, $request->database_name)){

            $results = $this->EnvironmentManager->saveFileWizard($request);

            $message = (isset($results['message']) ? $results['message'] : '');
            $response = (isset($results['response']) ? $results['response'] : '');

            if($response == false){
                session(['envConfigData' => $this->EnvironmentManager->fileData($request)]);
                return $redirect->route('LaravelInstaller::environmentClassic')
                ->with(['message' => empty($message) ? trans('installer_messages.environment.errors') : $message]);
            }

            event(new EnvironmentSaved($request));

            return $redirect->route('LaravelInstaller::database')
                            ->with(['results' => $results]);
        }else{
            return $redirect->route('LaravelInstaller::environmentWizard');
        }
    }
}
