#!/bin/bash -x

APPLICATION_DOT_CONFIG_ENABLE="ENABLE"

#   ----------------------------------------------------------------------------
#   Module names must be specified with proper casing.
#   ----------------------------------------------------------------------------
TEMPLATE_MODULE="Video"
NEW_MODULE="ContactManagement"

#   ----------------------------------------------------------------------------
#   Statically set the Zend Framework 2 Module path
#   ----------------------------------------------------------------------------
BASE_PATH="`pwd`"
MODULE_PATH="`pwd`/module/"

#   ----------------------------------------------------------------------------
#   Convert the module names to lowercase
#   ----------------------------------------------------------------------------
LC_TEMPLATE_MODULE="${TEMPLATE_MODULE,,}"
LC_NEW_MODULE="${NEW_MODULE,,}"

#   ----------------------------------------------------------------------------
#   Change into the module path
#   ----------------------------------------------------------------------------
cd ${MODULE_PATH}

#   Copy (recursively) the old module to the new module path.
#   This will create module/${FIRST_LTR_UPPER_NEW_MODULE}
#   ----------------------------------------------------------------------------
#   Recursively copy the Template Module to the new module's path.
#   ----------------------------------------------------------------------------
#   - This will create the new module at the path:
#       ${MODULE_PATH}{FIRST_LTR_UPPER_NEW_MODULE}
cp -rp ${MODULE_PATH}${TEMPLATE_MODULE}/ ${MODULE_PATH}${NEW_MODULE}/

#   ----------------------------------------------------------------------------
#   Set the new module's file permissions to be open
#   ----------------------------------------------------------------------------
#   - Note: This is a questionable practice, should be reworked.
#       - Better practice might be to copy the directory permissions as well.
#   chmod -R 777 ${NEW_MODULE}

#   ----------------------------------------------------------------------------
#   Change into the path of the new module
#   ----------------------------------------------------------------------------
cd ${MODULE_PATH}${NEW_MODULE}

#   ----------------------------------------------------------------------------
#   Replace all occurences of uppercase string (sets up Namespace)
#   ----------------------------------------------------------------------------
find . -type f -print0 | xargs -0 sed -i "s|${TEMPLATE_MODULE}|${NEW_MODULE}|g"

#   ----------------------------------------------------------------------------
#   Replace all occurences of lowercase string
#   ----------------------------------------------------------------------------
find . -type f -print0 | xargs -0 sed -i "s|${LC_TEMPLATE_MODULE}|${LC_NEW_MODULE}|g"

#   ----------------------------------------------------------------------------
#   Rename all regular-cased files
#   ----------------------------------------------------------------------------
#   - Not working right now.
#for i in `find . -name "*${TEMPLATE_MODULE}*"` ; do mv $i `echo $i | sed "s|${TEMPLATE_MODULE}|${NEW_MODULE}|"` ; done

#   ----------------------------------------------------------------------------
#   Rename all lowercased files
#   ----------------------------------------------------------------------------
#   - Not working right now.
#for i in `find . -name "*${LC_TEMPLATE_MODULE}*"` ; do mv $i `echo $i | sed "s|${LC_TEMPLATE_MODULE}|${LC_NEW_MODULE}|"` ; done

#   ----------------------------------------------------------------------------
#   Rename all files and folders with the new module name
#   ----------------------------------------------------------------------------
PREFIX="${MODULE_PATH}${NEW_MODULE}/"

mv ${PREFIX}src/${TEMPLATE_MODULE} ${PREFIX}src/${NEW_MODULE}
mv ${PREFIX}src/${NEW_MODULE}/Controller/${TEMPLATE_MODULE}Controller.php ${PREFIX}src/${NEW_MODULE}/Controller/${NEW_MODULE}Controller.php
mv ${PREFIX}src/${NEW_MODULE}/Form/${TEMPLATE_MODULE}Form.php ${PREFIX}src/${NEW_MODULE}/Form/${NEW_MODULE}Form.php
mv ${PREFIX}src/${NEW_MODULE}/Model/${TEMPLATE_MODULE}.php ${PREFIX}src/${NEW_MODULE}/Model/${NEW_MODULE}.php
mv ${PREFIX}src/${NEW_MODULE}/Model/${TEMPLATE_MODULE}Table.php ${PREFIX}src/${NEW_MODULE}/Model/${NEW_MODULE}Table.php
mv ${PREFIX}view/${LC_TEMPLATE_MODULE}/ ${PREFIX}view/${LC_NEW_MODULE}/
mv ${PREFIX}view/${LC_NEW_MODULE}/${LC_TEMPLATE_MODULE}/ ${PREFIX}view/${LC_NEW_MODULE}/${LC_NEW_MODULE}/
#   ----------------------------------------------------------------------------
#   Auto-generate the MySQL table
#   ----------------------------------------------------------------------------
#   - Chris note to self: Set this up so that it will auto-create a table
#       using a *.sql command in the proper MySQL database
#
#


#   ----------------------------------------------------------------------------
#   Enable the module in Application configuration if specified
#   ----------------------------------------------------------------------------
#   - Note: This isn't working right now
if [ -z ${APPLICATION_DOT_CONFIG_ENABLE} ]; then
    APPLICATION_DOT_CONFIG_ENABLE="do-not-enable-new-module-in-application-dot-config"
fi

if [ ${APPLICATION_DOT_CONFIG_ENABLE,,} = "enable" ]
then
    cd ${MODULE_PATH} && cd ../config/
    sed "s|'${TEMPLATE_MODULE}',|'${TEMPLATE_MODULE}',\n'${NEW_MODULE}',|g" application.config.php
    cd ../../
fi

#   ----------------------------------------------------------------------------
#   Regenerate all of the classmap files.
#   ----------------------------------------------------------------------------
cd ${BASE_PATH}
./classmaps.sh

