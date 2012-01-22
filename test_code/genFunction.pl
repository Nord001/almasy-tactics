#!/usr/bin/perl

use strict;

open(FILE, "functionList.txt") or die("Could not open");
open(OUT, ">output.txt");

my @lines = <FILE>;

my %table;

foreach (@lines) {
  chomp;
  if (m/(.*).php:.*function ([^ ]*)/) {
    print $1 . " " . $2 . ": 1) view 2) state-changing 3) ajax: ";
    my $ans = <>;
    chomp $ans;
    $table{$1} = [] if ($table{$1} eq '');
    push @{$table{$1}}, sprintf('%s: %s', $2, $ans);
  }
}

foreach my $key (keys %table) {
  print OUT $key . "\n";
  foreach my $entry (@{$table{$key}}) {
    print OUT $entry . "\n";
  }
  print OUT "\n";
}
