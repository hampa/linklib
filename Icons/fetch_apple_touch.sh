#!/bin/sh

agent='Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1A543 Safari/419.3'

wget -U "Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16" soundism.com -O hej.txt
#wget -U "Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16" spotify.com -O hej.txt
exit

for x in youtube.com spotify.com kritiker.se songlyrics.com dn.se  timbuk.nu buttericks.se kulturmejeriet.se lyricstranslate.com  junkyard.se sef.nu apple.com aftonbladet.se; do
	wget -U "Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16" "http://www.$x" -O - | grep touch-icon >> icons.txt
done
