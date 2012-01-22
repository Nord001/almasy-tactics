use strict;

open(FILE, 'query.log') or die('Could not open query log.');

my @lines = <FILE>;

my %frequenciesByQuery;
my %timeByQuery;
my $timeTotal;

foreach (@lines) {
	if (/Query: ([^:]*): (.*)/) {
		my $query = $1;
		my $time = $2;

		$frequenciesByQuery{$query}++;
		$timeByQuery{$query} += $time;
		$timeTotal += $time;
	}
}

print "Frequencies by Query\n\n";
foreach my $key (keys(%frequenciesByQuery)) {
	printf("%-35s %6s\n", $key, $frequenciesByQuery{$key});
}

print "\n\nTime by Query\n\n";
foreach my $key (keys(%timeByQuery)) {
	my $ms = sprintf('%d', $timeByQuery{$key} * 1000);
	printf("%-35s %6sms\n", $key, $ms);
}

print "\n\nAverage Time by Query\n\n";
foreach my $key (keys(%timeByQuery)) {
	my $ms = sprintf('%d', $timeByQuery{$key} / $frequenciesByQuery{$key} * 1000);
	printf("%-35s %6sms\n", $key, $ms);
}


print "\n\n% Time by Query\n\n";
foreach my $key (keys(%timeByQuery)) {
	my $timePercent = sprintf('%.1f', $timeByQuery{$key} / $timeTotal * 100);
	printf("%-35s %6s%%\n", $key, $timePercent);
}