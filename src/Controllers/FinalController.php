<?php

namespace Softnio\LaravelInstaller\Controllers;

use Illuminate\Routing\Controller;
use Softnio\LaravelInstaller\Helpers\EnvironmentManager;
use Softnio\LaravelInstaller\Helpers\FinalInstallManager;
use Softnio\LaravelInstaller\Helpers\InstalledFileManager;
use Softnio\LaravelInstaller\Events\LaravelInstallerFinished;

class FinalController extends Controller
{
    /**
     * Update installed file and display finished view.
     *
     * @param \Softnio\LaravelInstaller\Helpers\InstalledFileManager $fileManager
     * @param \Softnio\LaravelInstaller\Helpers\FinalInstallManager $finalInstall
     * @param \Softnio\LaravelInstaller\Helpers\EnvironmentManager $environment
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function finish(InstalledFileManager $fileManager, FinalInstallManager $finalInstall, EnvironmentManager $environment)
    {
        $finalMessages = $finalInstall->runFinal();
        $finalStatusMessage = $fileManager->update();
        $finalEnvFile = $environment->getEnvContent();

        event(new LaravelInstallerFinished);

        return view('vendor.installer.finished', compact('finalMessages', 'finalStatusMessage', 'finalEnvFile'));
    }
}
