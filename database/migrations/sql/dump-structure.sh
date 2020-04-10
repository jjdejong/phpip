# Use only on a database populated with the initial default data, NOT on a database in production
# Tailor to your specific database connection
mysqldump -u root -p --routines --events --no-data --column-statistics=0 phpip_tracker | sed 's/ AUTO_INCREMENT=[0-9]*//g' | sed 's/`%`/`localhost`/g' > phpip-structure.sql
mysqldump -u root -p --no-create-info --skip-triggers --column-statistics=0 --default-character-set=utf8 phpip_tracker | sed 's/),(/),\
(/g' > phpip-data.sql
