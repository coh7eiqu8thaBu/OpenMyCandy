rm -f xor.exe
gcc -static -Wall -O4 xor.c -o xor.exe
ret=$?
if [ $ret -ne 0 ]
then
  echo ERREUR
  exit
else
  ls -al xor.exe
  strip xor.exe
  ls -al xor.exe
fi
upx -9 xor.exe
./test.sh
