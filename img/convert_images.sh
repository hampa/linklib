#!/bin/sh

for x in *.jpg; do
	f="${x%.*}"
	convert $x $f.png
done
for x in *.png; do
	echo $x
	convert -resize '460x' -quality 50% $x* 460/$x
done
