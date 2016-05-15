#!/bin/bash
#
dd if=/dev/zero    of=test.zero.dat bs=64 count=1 >/dev/null 2>&1
dd if=/dev/urandom of=test.rand.dat bs=64 count=1 >/dev/null 2>&1


for FICTEST in test.zero.dat test.rand.dat
do
  echo "MD5 de référence :"
  MD5SUM0=`md5sum.exe ${FICTEST} | awk '{ print $1 }'`
  echo $MD5SUM0
  for EXTENTION in " " "-s"
  do
    for CLEF in 1234 12345678 azertyuiopqsdfghjklmwxcvbnAZERTYUIQSDFGHWXCVB
    do
      ./xor.exe -k ${CLEF} ${EXTENTION} -i ${FICTEST} -o xor.xor
      MD5SUM1=`md5sum.exe xor.xor | awk '{ print $1 }'`
      MD5SUM2=`cat xor.xor | ./xor.exe -k ${CLEF} ${EXTENTION} | md5sum.exe - | awk '{ print $1 }'`
      if [ "$MD5SUM1" != "$MD5SUM2" -a "$MD5SUM0" == "$MD5SUM2" ]
      then
        echo "SUCESS - $MD5SUM0 == $MD5SUM2 - Clef : ${CLEF}"
      else
        echo "FAIL ! - $MD5SUM0 != $MD5SUM2 - Clef : ${CLEF}"
      fi
    done
  done
done

rm -f xor.xor
