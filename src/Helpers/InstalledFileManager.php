<?php

namespace Softnio\LaravelInstaller\Helpers;

use Exception;

class InstalledFileManager
{
    /**
     * Create installed file.
     *
     * @return int
     */
    public function create()
    {
        $installedLogFile = storage_path('installed');



        $dateStamp = date("Y/m/d h:i:sa");

        if (!file_exists($installedLogFile))
        {
            $message = trans('installer_messages.installed.success_log_message') . $dateStamp . "\n";

            file_put_contents($installedLogFile, $message);

        } else {
            $message = trans('installer_messages.updater.log.success_message') . $dateStamp;

            file_put_contents($installedLogFile, $message.PHP_EOL , FILE_APPEND | LOCK_EX);
        }

        return $message;
    }

    /**
     * Update installed file.
     *
     * @return int
     */
    public function update()
    {
        return $this->create();
    }

    /**
     * Check Storage write permission
     *
     * @return bool
     */
    private function check_storage()
    {
        $file = storage_path('preinstalled');
        try {
            file_put_contents($file, "Checking file write permission in storage path.");
            if(file_exists($file) && ( ! is_dir($file))){
                unlink($file);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}