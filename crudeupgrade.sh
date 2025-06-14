file=$1
#pass in filename as argument and do our hard coded upgrade process
#todo check if file was backed up correctly before delete (hence the cp vs mv)
#todo append date or increment so we don't overwrite any other backups with the same name
cp ./dest/$file ./backup/$file.$(date "+%Y%m%d_+%H-%M")
echo "Backed up live file" + $file
rm ./dest/$file
echo "Deleted live file"
cp ./source/$file ./dest/$file
echo "Copied preprocess file to live"
rm ./source/$file
echo "done"
exit 0
