file=$1
#pass in filename as argument and do our hard coded upgrade process                                                                                                                                                                          cp ./live/$file ./backup/$file.$(date '+%Y%m%d')
#todo check if file was backed up correctly before delete (hence the cp vs mv)
echo "Backed up live file" + $file
rm ./live/$file
echo "Deleted live file"
cp ./preprocess/$file ./live/$file
echo "Copied preprocess file to live"
echo "done"
exit 0
