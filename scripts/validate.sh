#!/bin/bash
export PATH="/usr/sbin:/sbin:/usr/local/bin:/usr/bin:/bin"
BOLD="\e[1m"
RED="\e[31m"
GREEN="\e[92m"
RESET="\e[0m"

function format_echo() {
  echo -e "${1}${2}${RESET}"
}

#if [[ $@ -ne 1 ]]; then
#    format_echo ${RED} "Please run the script with only one argument."
#    exit 1
#fi
ERRORS=0
for TABLE in $(cat scripts/table_list.txt); do
    ROWS=$(mysql zendump -e "select count(*) from ${TABLE}\G" | \
        tail -1 | \
        cut -f2 -d\: | \
        sed 's/ //g')
    if [[ ${ROWS} -gt 0 ]]; then
        format_echo ${GREEN} "${TABLE} contains data."
    else
        format_echo ${RED} "${TABLE} contains no data."
        let ERRORS=${ERRORS}+1
    fi
done
if [[ ${ERRORS} -eq 0 ]]; then
    format_echo ${GREEN} "All tables seem to contain valid data."
else
    format_echo ${RED} "Tables were found with no data, please check."
fi
