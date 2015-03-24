 #/bin/bash -x
#
# ------------------------------------------------------------------------------
# Run this script: ./classmaps.sh
#
# Generates ZF2 Classmap Output
# ------------------------------------------------------------------------------

cd module/
echo -e "Status: Regenerating the project's classmaps"
for D in *; do
    if [ -d "${D}" ]; then
        cd ${D}
        php -f ../../vendor/zendframework/zendframework/bin/classmap_generator.php &>/dev/null
        cd ../
    fi
done
echo -e "Finished: Congratulations, the project classmaps have been successfully regenerated"
echo -e "We recommend that you commit the new classmap files to Git"
