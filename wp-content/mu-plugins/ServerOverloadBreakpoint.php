<?php

namespace ServerOverLoadBreakpoint;

class ServerOverloadBreakpoint
{
    public $MaxApacheProcesses = 200;
    public $domain = 'intranat.helsingborg.se';

    public function __construct()
    {
        if ($_SERVER['HTTP_HOST'] != $this->domain) {
            return true;
        }

        if ($this->isLockedIn()) {
            return true;
        }

        if ($this->isLockedOut()) {
            $this->sendErrorMessage("Avvaktar tillgängliga resurser.");
        }

        if (!$this->hasFreeCPU()) {
            $this->sendErrorMessage("Avvaktar ledig beräkningskraft.");
        }

        if (!$this->hasFreeApacheProcesses()) {
            $this->sendErrorMessage("Avvaktar lediga processer.");
        }

        // Make the user a VIP
        $this->doLockin();
    }

    private function hasFreeApacheProcesses()
    {
        exec('ps aux | grep apache', $output);

        if (count($output) >= $this->MaxApacheProcesses) {
            return false;
        }

        return true;
    }

    private function hasFreeCPU()
    {
        if (sys_getloadavg()[0] < 0.8) {
            return true;
        }
        return false;
    }

    private function isLockedOut()
    {
        if (isset($_COOKIE['overloaded-server-lockout']) && $_COOKIE['overloaded-server-lockout'] == 1) {
            return true;
        }
        return false;
    }

    private function isLockedIn()
    {
        if (isset($_COOKIE['overloaded-server-lockin']) && $_COOKIE['overloaded-server-lockin'] == 1) {
            return true;
        }
        return false;
    }

    private function doLockout()
    {
        if (!$this->isLockedOut()) {
            setcookie("overloaded-server-lockout", 1, time()+120);
        }
    }

    private function doLockin()
    {
        setcookie("overloaded-server-lockin", 1, time()+600);
    }

    public function sendErrorMessage($reason = "")
    {
        $this->doLockout();
        die('
            <style>
                body {
                    font-family: Helvetica;
                    line-height: 140%;
                    font-size: 16px;
                }

                div {
                    max-width: 500px;
                    margin: 10% auto;
                    outline: 1px solid #e2e2e2;
                    padding: 20px;
                    background: rgba(233, 233, 233, 0.42);
                    box-shadow: 0px 2px 7px rgba(0, 0, 0, 0.2);
                }

                h1 {
                    margin-top: 0;
                }

                a {
                    display: block;
                    text-align: center;
                    padding: 10px 15px;
                    background: #e2e2e2;
                    text-decoration: none;
                    color: #000;
                    outline: 1px solid #dadada;
                }

            </style>
            <script>setTimeout("location.reload(true);", (140 * 1000))</script>
            <div>
                <h1>Sidan kan inte visas</h1>
                <p>Tyvärr kan vi inte hantera din förfrågan för tillfället eftersom för många användare besöker intranätet just nu.</p>
                <a href="https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '">Försök igen</a>
            </div>
            <p style="font-size: 0.7em; text-align: center;">Felsökningsinformation: ' .$reason. '</p>
        ');
    }
}

new \ServerOverLoadBreakpoint\ServerOverLoadBreakpoint();
