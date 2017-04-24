<?php
/**
 *  This file will process sweepstakes entries with a provided csv
 *  file.
 *
 *  @author Doug Morin dougmorin0@gmail.com
 */

// Set the default timezone to stop strtotime from bitching.
date_default_timezone_set("America/New_York");

/**
 *  Function get()
 *
 *    @param (string) $message This variable contains the message displayed before it requests user input from the keyboard
 *    @param (string) $additional_checks This paramater will provide additional checks to validate the data
 */
function get($message, $additional_checks = false) {
  do {
    echo $message . " ";
    $input = trim(fgets(STDIN));

    $trigger = false;
    if ($additional_checks !== false) {

      switch ($additional_checks) {
        case 'csv':
          if (!file_exists($input)) {
            $trigger = true;
            echo "Please enter a valid file to continue.\n\n";
          }
          break;
        case 'date':
          if (strlen($input) != 10 || substr($input, 2, 1) != '/' || substr($input, 5, 1) != '/') {
            $trigger = true;
            echo "The date you entered is not in the correct format.\n\n";
          }
          break;
        case 'time':
          if (strlen($input) != 8 || substr($input, 2, 1) != ':' || substr($input, 5, 1) != ':') {
            $trigger = true;
            echo "The time you entered is not in the correct format.\n\n";
          }
          break;
        case 'numeric':
          $input = (int) $input;
          if (!is_numeric($input) || $input < 0) {
            $trigger = true;
            echo "The number you entered was not valid.\n\n";
          }
          break;
        case 'yesno':
          $input = $input;
          if ($input != 'yes' && $input != 'no') {
            $trigger = true;
            echo "Please enter 'yes' or 'no'.\n\n";
          }
          break;
      }
    }
  } while ($input == NULL || $trigger);
  echo "\n";
  return $input;
}

/**
 * This function calculates random numbers
 *
 * @param (int) @max This var is the ceiling for the random number
 * @param (int) $entries_per_day This var is essentially the number of random numbers that we need to pass back
 */
function getRandomEntries($max, $entries_per_day) {
  $rnds = array();

  // Loop through the amount of entries that we need to pass back
  for ($i = 0; $i < $entries_per_day; $i++) {
    // Generate a rnd number until we get one that isn't already used
    do {
      $trigger = false;
      $rnd_number = mt_rand(0, $max);
    } while (in_array($rnd_number, $rnds));

    // Save the new random number
    $rnds[] = $rnd_number;

    // Clear the old random number variable
    $rnd_number = "";
  }

  // return ($entries_per_day == 1) ? $rnds[0] : $rnds;
  return $rnds;
}

/**
 * Fills provides a comma string to provide cell spacing for csv's
 *
 *  @param $spaces The number of cells to fill and return.
 */
function getCsvFill($spaces) {
  $return = array();
  for ($i = 0; $i < $spaces; $i++) {
    $return[] = " ";
  }
  return $return;
}

// Clear the screen
passthru('clear');

// Echo out the directions

echo "********************************************************************\n";
echo "\n";
echo " This script will process sweepstakes entries with a provided CSV\n";
echo " file.\n";
echo "\n";
echo " Requirements:\n";
echo "\n";
echo "  1: The CSV file must be downloaded onto your local machine\n";
echo "  2: There must be at least one date field inside the CSV that can\n";
echo "     be read by php and parsed to a timestamp with the strtotime()\n";
echo "     function\n";
echo "  3: The entries must be ordered by the date row specified above.\n";
echo "  4: There must be at least one email field inside the CSV so that\n";
echo "     we can make sure that there are no duplicates\n";
echo "\n";
echo "********************************************************************\n";
echo "\n";

// Get the location of the CSV file that needs to be processed.
$csv = get("Local CSV path (full path):", 'csv');

// Get the start date
$startdate = get("Start date (ex: 01/02/2015):", 'date');

// Get the end date
$enddate = get("End date (ex: 01/02/2015):", 'date');

// Get the start time
$starttime = get("Start time (ex: 02:00:00 or 13:00:00):", 'time');

// Get the end time
$endtime = get("End time (ex: 02:00:00 or 13:00:00):", 'time');

// Get the amount of entries per day that we want to calculate
$entries_per_day = get("Entries per day to pull:", 'numeric');

// Get the amount of entries per day that we want to calculate
$grand_prize_entries = get("Number of grand prize entries:", 'numeric');

// Set the date incrementer
$reference_day_count = 1;

// Start Date/Time Timestamp
$start_timestamp = strtotime($startdate . " " . $starttime);

// End Date/Time Timestamp
$end_timestamp = strtotime($enddate . " " . $endtime);

// Set the initial reference timestamp
$original_reference_timestamp = strtotime($startdate . " " . $endtime);

$reference_timestamp = strtotime("+1 day", $original_reference_timestamp);

// Beginning Date/Time
$beginning_date = date("Y-m-d H:i:s", $start_timestamp);

// End Date/Time
$end_date = date("Y-m-d H:i:s", $end_timestamp);

// Reference date time.  We will increment this as we compare the dates on the CSV.
$reference_date = date("Y-m-d H:i:s", $reference_timestamp);

// Row counter
$row = 1;

// Array for the current reference days entries
$current_days_entries = array();

// Sets the row count
$csv_row_count = 0;

$temp_reference_display_date_begin = $beginning_date;

$saved_entries = array();

$grand_prize_winner = array();


// Loop through the entries to get the data
if (($handle = fopen($csv, "r")) !== FALSE) {
  while (($data = fgetcsv($handle)) !== FALSE) {
    // Get the amount of columns in the CSV
    $num = count($data);

    // If we're dealing with the header row, then we need to determine the appropriate date field for reference.
    if ($row == 1) {
      // Set the row count based off of the header
      $csv_row_count = count($data);

      $saved_entries[] = $data;

      // Show the column options to the user
      foreach ($data as $key => $value) {
        echo "[" . ($key + 1)  . "] - " . $value . "\n";
      }
      echo "\n";

      // Prompt to ask the user what field they would like to use for the date field
      do {
        $date_field = get("Which field would you like to use as the date reference?", 'numeric');
        if ($date_field > $num) {
          echo "Please enter a number between 1 - " . $num . "\n";
        }
      } while ($date_field > $num);

      // Decrement the date_field reference.  We had to increment it in the loop because the user could not enter [0] as an option.
      $date_field--;

      // Echo the user's choice.
      echo "You have chosen to use the field: [" . $data[$date_field] . "]\n";
      echo "\n";

      // Prompt to ask the user what field they would like to use for the email field
      do {
        $email_field = get("Which field would you like to use as the email reference?");
        if ($email_field > $num) {
          echo "Please enter a number between 1 - " . $num . "\n";
        }
      } while ($email_field > $num);

      // Decrement the email_field reference.  We had to increment it in the loop because the user could not enter [0] as an option.
      $email_field--;

      // Echo the user's choice.
      echo "You have chosen to use the field: [" . $data[$email_field] . "]\n";
      echo "\n\n";
      echo "Pulling entries!\n\n";
    }

    // Process the entries
    else {
      // Get the date and tear that shit up.
      $current_timestamp = strtotime($data[$date_field]);

      // Row debugging
      if (!isset($data[$email_field]) || !isset($data[$date_field])) {
        echo "\n*************\n";
        echo "Debugging: We found a row that didn't match the others.  Please investigate!\n";
        print_r($data);
        echo "\n*************\n";
      }

      // Add this player to the grand prize array
      if (!in_array($data[$email_field], $grand_prize_winner)) {
        $grand_prize_winner[] = $data[$email_field];
      }

      // Determine if we need to process the entries inside of the $current_days_entries file
      if ($current_timestamp > $reference_timestamp) {
        echo "There were " . count($current_days_entries) . " total entries by " . $reference_date . "\n";

        // Loop through and make sure the winners selected have not already been selected
        do {
          $winners = getRandomEntries(count($current_days_entries)-1, $entries_per_day);

          $trigger_email = false;

          foreach ($saved_entries as $se) {
            foreach ($winners as $key => $value) {
              if (!$trigger_email && $current_days_entries[$value][$email_field] == $se[$email_field]) {
                $trigger_email = true;
              }
            }
          }
        } while ($trigger_email);

        // loop through and output the winners
        $tempcount = 1;
        foreach ($winners as $key => $value) {
          $saved_entries[] = $current_days_entries[$value];
          $tempcount++;
        }

        $temp_reference_display_date_begin = $reference_date;

        // Set the new $reference_day_count
        $reference_day_count++;

        // Set the new reference timestamp
        $reference_timestamp = strtotime("+" . $reference_day_count . " days", $original_reference_timestamp);

        // Set the reference date/time
        $reference_date = date("Y-m-d H:i:s", $reference_timestamp);

        // Exit the loop if the entry is past or at the end date/time
        if ($reference_date > $end_date) {
          break;
        }
      }

      $current_days_entries[] = $data;
    }

    $row++;
  }

  fclose($handle);

  // set the number of unique entries;
  $unique_entries = count($grand_prize_winner);

  $generated_entries = count($saved_entries);
  $generated_entries--;

  echo "Generated " . $generated_entries . " entries!";
  echo "\n";

  echo "Unique email entries: " . $unique_entries . "\n";
  echo "\n";

  // Get the final grand prize winner(s).
  $gpr_winner = array();

  for ($i = 0; $i < $grand_prize_entries; $i++) {
    do {
      $trigger_email = false;
      $grand_prize_rnd = getRandomEntries(count($grand_prize_winner)-1, 1);
      $grand_prize_rnd = $grand_prize_rnd[0];

      // Out of bounds, exit the check
      if (!isset($grand_prize_winner[$grand_prize_rnd])) {
        $trigger_email = true;
      }

      // In bounds, use the rnd number
      else {
        foreach ($saved_entries as $se) {
          // Make sure the grand prize winner has not won anything else
          if (!$trigger_email && $grand_prize_winner[$grand_prize_rnd] == $se[$email_field]) {
            $trigger_email = true;
          }
        }
      }
    } while ($trigger_email);

    // Add the checked row to the grand prize rands winner array
    $gpr_winner[] = $grand_prize_winner[$grand_prize_rnd];
  }

  // Place an empty row
  $saved_entries[] = getCsvFill($csv_row_count);

  // Loop through the entries to get the data
  if (($handle = fopen($csv, "r")) !== FALSE) {
    while (($data = fgetcsv($handle)) !== FALSE) {
      if (in_array($data[$email_field], $gpr_winner)) {
        $saved_entries[] = $data;
        echo "The grand prize winner is: " . $data[$email_field] . "\n";
      }
    }
    fclose($handle);
  }

  // Place an empty row
  $saved_entries[] = getCsvFill($csv_row_count);

  // Save the unique entries to the csv
  $saved_entries[] = array_merge(array("Unique Entries", $unique_entries), getCsvFill($csv_row_count-2));

  $winners_file = $csv . '.winners.csv';

  // Write the winners file
  $fp = fopen($winners_file, 'w');

  foreach ($saved_entries as $fields) {
    fputcsv($fp, $fields);
  }

  echo "Entries have been written to " . $winners_file . "\n";

  fclose($fp);
}
