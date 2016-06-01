<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Calendar extends CI_Calendar{
    public function __construct($config = array()){
        $this->CI =& get_instance();
        $this->CI->lang->load('calendar');

        empty($config) OR $this->initialize($config);

        log_message('info', 'Calendar Class Initialized');
    }

    // --------------------------------------------------------------------

    public function generate($year = '', $month = '', $data = array())
    {
        $local_time = time();

        // Set and validate the supplied month/year
        if (empty($year))
        {
            $year = date('Y', $local_time);
        }
        elseif (strlen($year) === 1)
        {
            $year = '200'.$year;
        }
        elseif (strlen($year) === 2)
        {
            $year = '20'.$year;
        }

        if (empty($month))
        {
            $month = date('m', $local_time);
        }
        elseif (strlen($month) === 1)
        {
            $month = '0'.$month;
        }

        $adjusted_date = $this->adjust_date($month, $year);

        $month	= $adjusted_date['month'];
        $year	= $adjusted_date['year'];

        // Determine the total days in the month
        $total_days = $this->get_total_days($month, $year);

        // Set the starting day of the week
        $start_days	= array('sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6);
        $start_day	= isset($start_days[$this->start_day]) ? $start_days[$this->start_day] : 0;

        // Set the starting day number
        $local_date = mktime(12, 0, 0, $month, 1, $year);
        $date = getdate($local_date);
        $day  = $start_day + 1 - $date['wday'];

        while ($day > 1)
        {
            $day -= 7;
        }

        // Set the current month/year/day
        // We use this to determine the "today" date
        $cur_year	= date('Y', $local_time);
        $cur_month	= date('m', $local_time);
        $cur_day	= date('j', $local_time);

        $is_current_month = ($cur_year == $year && $cur_month == $month);

        // Generate the template data array
        $this->parse_template();

        // Begin building the calendar output
        $out = $this->replacements['table_open']."\n\n".$this->replacements['heading_row_start']."\n";
//Changed
        // "previous" month link
        if ($this->show_next_prev === TRUE)
        {
            // Add a trailing slash to the URL if needed
            $this->next_prev_url = preg_replace('/(.+?)\/*$/', '\\1/', $this->next_prev_url);

            $adjusted_date = $this->adjust_date($month - 1, $year);
 /*-->*/    if(stripos($this->next_prev_url,'(') !== FALSE && stripos($this->next_prev_url,')') !== FALSE){
                $str_start = substr($this->next_prev_url, 0, stripos($this->next_prev_url,'(') + 1);
                $str_end = substr($this->next_prev_url, stripos($this->next_prev_url,')'), 1);
                $out .= str_replace('{previous_url}', $str_start.$adjusted_date['year'].','.$adjusted_date['month'].$str_end, $this->replacements['heading_previous_cell'])."\n";
            }else
                $out .= str_replace('{previous_url}', $this->next_prev_url.$adjusted_date['year'].'/'.$adjusted_date['month'], $this->replacements['heading_previous_cell'])."\n";
        }
//!Changed
        // Heading containing the month/year
        $colspan = ($this->show_next_prev === TRUE) ? 5 : 7;

        $this->replacements['heading_title_cell'] = str_replace('{colspan}', $colspan,
                                str_replace('{heading}', $this->get_month_name($month).'&nbsp;'.$year, $this->replacements['heading_title_cell']));

        $out .= $this->replacements['heading_title_cell']."\n";
//Changed
        // "next" month link
        if ($this->show_next_prev === TRUE)
        {
            $adjusted_date = $this->adjust_date($month + 1, $year);
/*-->*/     if(stripos($this->next_prev_url,'(') !== FALSE && stripos($this->next_prev_url,')') !== FALSE){
                $str_start = substr($this->next_prev_url, 0, stripos($this->next_prev_url,'(') + 1);
                $str_end = substr($this->next_prev_url, stripos($this->next_prev_url,')'), 1);
                $out .= str_replace('{next_url}', $str_start.$adjusted_date['year'].','.$adjusted_date['month'].$str_end, $this->replacements['heading_next_cell'])."\n";
            }else
                $out .= str_replace('{next_url}', $this->next_prev_url.$adjusted_date['year'].'/'.$adjusted_date['month'], $this->replacements['heading_next_cell'])."\n";
        }
//!Changed
        $out .= "\n".$this->replacements['heading_row_end']."\n\n"
            // Write the cells containing the days of the week
            .$this->replacements['week_row_start']."\n";

        $day_names = $this->get_day_names();

        for ($i = 0; $i < 7; $i ++)
        {
            $out .= str_replace('{week_day}', $day_names[($start_day + $i) %7], $this->replacements['week_day_cell']);
        }

        $out .= "\n".$this->replacements['week_row_end']."\n";

        // Build the main body of the calendar
        while ($day <= $total_days)
        {
            $out .= "\n".$this->replacements['cal_row_start']."\n";

            for ($i = 0; $i < 7; $i++)
            {
                if ($day > 0 && $day <= $total_days)
                {
                    $out .= ($is_current_month === TRUE && $day == $cur_day) ? $this->replacements['cal_cell_start_today'] : $this->replacements['cal_cell_start'];

                    if (isset($data[$day]))
                    {
                        // Cells with content
                        $temp = ($is_current_month === TRUE && $day == $cur_day) ?
                                $this->replacements['cal_cell_content_today'] : $this->replacements['cal_cell_content'];
                        $out .= str_replace(array('{content}', '{day}'), array($data[$day], $day), $temp);
                    }
                    else
                    {
                        // Cells with no content
                        $temp = ($is_current_month === TRUE && $day == $cur_day) ?
                                $this->replacements['cal_cell_no_content_today'] : $this->replacements['cal_cell_no_content'];
                        $out .= str_replace('{day}', $day, $temp);
                    }

                    $out .= ($is_current_month === TRUE && $day == $cur_day) ? $this->replacements['cal_cell_end_today'] : $this->replacements['cal_cell_end'];
                }
                elseif ($this->show_other_days === TRUE)
                {
                    $out .= $this->replacements['cal_cell_start_other'];

                    if ($day <= 0)
                    {
                        // Day of previous month
                        $prev_month = $this->adjust_date($month - 1, $year);
                        $prev_month_days = $this->get_total_days($prev_month['month'], $prev_month['year']);
                        $out .= str_replace('{day}', $prev_month_days + $day, $this->replacements['cal_cell_other']);
                    }
                    else
                    {
                        // Day of next month
                        $out .= str_replace('{day}', $day - $total_days, $this->replacements['cal_cell_other']);
                    }

                    $out .= $this->replacements['cal_cell_end_other'];
                }
                else
                {
                    // Blank cells
                    $out .= $this->replacements['cal_cell_start'].$this->replacements['cal_cell_blank'].$this->replacements['cal_cell_end'];
                }

                $day++;
            }

            $out .= "\n".$this->replacements['cal_row_end']."\n";
        }

        return $out .= "\n".$this->replacements['table_close'];
    }
}
