<?php

namespace ExecutionTimer;

class Timer
{
    public static $start;
    public static $stop;

    public static $info = array(
        'ru_oublock'       => 'Number of times the filesystem had to perform output',
        'ru_inblock'       => 'Number of times the filesystem had to perform input',
        'ru_msgsnd'        => 'Number of IPC messages sent',
        'ru_msgrcv'        => 'Number of IPC messages received',
        'ru_maxrss'        => 'Maximum resident set size used (in kilobytes)',
        'ru_ixrss'         => 'Integral shared memory size (kilobytes)',
        'ru_idrss'         => 'Integral unshared data size (kilobytes)',
        'ru_minflt'        => 'Number of page reclaims (soft page faults)',
        'ru_majflt'        => 'Number of page faults (hard page faults)',
        'ru_nsignals'      => 'Number of signals received',
        'ru_nvcsw'         => 'Number of times a context switch resulted due to a process voluntarily giving up the processor before its time slice was completed',
        'ru_nivcsw'        => 'Number of times a context switch resulted due to a higher priority process becoming runnable or because the current process exceeded its time slice.',
        'ru_nswap'         => 'Numer of swaps',
        'ru_utime.tv_usec' => 'User CPU time used (microseconds)',
        'ru_utime.tv_sec'  => 'User CPU time used (seconds)',
        'ru_stime.tv_usec' => 'System CPU time used (microseconds)',
        'ru_stime.tv_sec'  => 'System CPU time used (seconds)',
    );

    /**
     * Start, get the current usage stats
     * @return void
     */
    public static function start()
    {
        self::$start = getrusage();
    }

    /**
     * Stop, get the current usage stats
     * @return void
     */
    public static function stop()
    {
        self::$stop = getrusage();
    }

    /**
     * Outputs the results
     * @return void
     */
    public static function display()
    {
        if (!self::$stop) {
            self::stop();
        }

        $keys = array_unique(array_keys(array_merge(self::$start, self::$stop)));

        $table = '
            <div style="padding:30px;">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Key</th>
                        <th>Start</th>
                        <th>Stop</th>
                        <th>Diff</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($keys as $key) {
            $table .= '
                <tr>
                    <td>' . $key . '<br><small>' . self::$info[$key] . '</small></td>
                    <td>' . self::$start[$key] . '</td>
                    <td>' . self::$stop[$key] . '</td>
                    <td>' . ((int)self::$stop[$key] - (int)self::$start[$key]) . '</td>
                </tr>
            ';
        }

        $table .= '</tbody></table></div>';

        echo $table;
    }
}

if (defined('RUN_EXECUTION_TIMER') && RUN_EXECUTION_TIMER) {
    \ExecutionTimer\Timer::start();

    add_action('shutdown', function () {
        \ExecutionTimer\Timer::display();
    });
}
