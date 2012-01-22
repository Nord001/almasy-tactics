#!/usr/bin/perl

use strict;

my $template = do { local(@ARGV, $/) = "template.php"; <> };

open(FILE, "output.txt");

my @lines = <FILE>;

for (my $i = 0; $i < @lines; $i++) {
  chomp $lines[$i];
  my $controllerUnderscore = $lines[$i];
  my @pieces = split '_', $controllerUnderscore;
  for (my $j = 0; $j < @pieces; $j++) {
    $pieces[$j] = ucfirst($pieces[$j]);
  }  
  my $controllerName = join '', @pieces;
  pop @pieces;
  my $shortControllerName = join '', @pieces;
  my $modelName = $shortControllerName;
  $modelName =~ s/s?$//;
  $i++;

  open(OUT, ">tests/$controllerUnderscore" . "_test.php");

  my $file = $template;
  $file =~ s/{{controllerName}}/$controllerName/g;
  $file =~ s/{{shortControllerName}}/$shortControllerName/g;
  $file =~ s/{{modelName}}/$modelName/g;

  my $testCases = "";

  while ($lines[$i] ne "\n") {
    chomp $lines[$i];
    if ($lines[$i] =~ m/(.*): (.*)/) {
      my $actionName = $1;
      my $optionStr = $2;

      my @pieces = split '_', $actionName;
      for (my $j = 0; $j < @pieces; $j++) { $pieces[$j] = ucfirst($pieces[$j]); }
      my $camelActionName = join '', @pieces;

      my @options = split '', $optionStr;
      my @optionFlags;
      foreach (@options) { $optionFlags[$_] = 1; }

      my ($testName, $testCase);

      my $ajaxDisableStr = $optionFlags[3] ? "        \$this->c->disableAjaxCheck = true;\n" : "";

      $testCases .= "\n    //=============================================================================================\n";

      # ========================= SUCCEEDS =========================
      $testName = 'test_' . $camelActionName . '_Succeeds';
      $testCase = <<DELIM;

    //---------------------------------------------------------------------------------------------
    function $testName () {
$ajaxDisableStr        \$this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        \$this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        \$this->c->$actionName(SOME_NUM);
        \$this->assertFalse(\$this->c->didFof);
        \$this->assertTrue(\$this->FlashMessageLacks("error"));
    }
DELIM
      $testCases .= $testCase;

      # ========================= USER AUTH MISMATCH =========================
      $testName = 'test_' . $camelActionName . '_UserIdMismatch';
      $testCase = <<DELIM;

    //---------------------------------------------------------------------------------------------
    function $testName () {
$ajaxDisableStr        \$this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        \$this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        \$this->c->$actionName(SOME_NUM);
        \$this->assertTrue(\$this->c->didFof);
    }
DELIM
      $testCases .= $testCase;

      if ($optionFlags[1]) {
        # ========================= NONEXISTENT =========================
        $testName = 'test_' . $camelActionName . '_NonexistentObject';
        $testCase = <<DELIM;

    //---------------------------------------------------------------------------------------------
    function $testName () {
$ajaxDisableStr        // Insert mock model code here
        // Mock model to always return false

        \$this->c->$actionName(SOME_NUM);
        \$this->assertTrue(\$this->c->didFof);
    }
DELIM
         $testCases .= $testCase;

        # ========================= MISSING ARGUMENT  =========================
        $testName = 'test_' . $camelActionName . '_MissingArgument';
        $testCase = <<DELIM;

    //---------------------------------------------------------------------------------------------
    function $testName () {
$ajaxDisableStr        \$this->c->$actionName();
        \$this->assertTrue(\$this->c->didFof);
    }
DELIM
         $testCases .= $testCase;
      }

      ################################ STATE CHANGES ###############################
      if ($optionFlags[2]) {
        # ========================= NO DATA =========================
        $testName = 'test_' . $camelActionName . '_NoData';
        $testCase = <<DELIM;

    //---------------------------------------------------------------------------------------------
    function $testName () {
$ajaxDisableStr        \$this->c->$actionName();
        \$this->assertTrue(\$this->c->didFof);
    }
DELIM
         $testCases .= $testCase;

        # ========================= CSRF =========================
        $testName = 'test_' . $camelActionName . '_InvalidCSRFToken';
        $testCase = <<DELIM;

    //---------------------------------------------------------------------------------------------
    function $testName () {
$ajaxDisableStr        \$this->setupCSRFTokenTest();
        \$this->c->$actionName();
        \$this->assertTrue(\$this->FlashMessageContains('error'));
        \$this->assertFalse(\$this->c->didFof);
    }
DELIM
         $testCases .= $testCase;


        # ========================= FAILED SAVE  =========================
        $testName = 'test_' . $camelActionName . '_FailedSave';
        $testCase = <<DELIM;

    //---------------------------------------------------------------------------------------------
    function $testName () {
$ajaxDisableStr        // Mock model to fail save

        \$this->c->$actionName();
        \$this->assertTrue(\$this->FlashMessageContains("error"));
        \$this->assertFalse(\$this->c->didFof);
    }
DELIM
         $testCases .= $testCase;
      }


      $i++;
    }
  }

  $file =~ s/{{testCases}}/$testCases/g;

  print OUT $file;

  close(OUT);
}
