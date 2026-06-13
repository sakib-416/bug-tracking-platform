#!/bin/bash

# Define colors for terminal output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}=======================================${NC}"
echo -e "${BLUE}  BugVault Vulnerable Lab Installer    ${NC}"
echo -e "${BLUE}=======================================${NC}"

# 1. Check and Start Services
echo -e "\n${GREEN}[+] Starting Apache and MySQL services...${NC}"
sudo systemctl start apache2 || echo -e "${RED}[!] Failed to start Apache${NC}"
sudo systemctl start mysql || echo -e "${RED}[!] Failed to start MySQL${NC}"

# 2. Deploy Files to Web Root
echo -e "${GREEN}[+] Deploying files to /var/www/html/bugvault...${NC}"
sudo mkdir -p /var/www/html/bugvault
sudo cp -r ./* /var/www/html/bugvault/
sudo chown -R www-data:www-data /var/www/html/bugvault/
sudo chmod -R 755 /var/www/html/bugvault/

# 3. Automated MySQL Database & User Setup
# (Using 'sudo mysql' bypasses password prompts for local root on most Linux distros)
echo -e "${GREEN}[+] Configuring Database and 'sakib' user automatically...${NC}"
sudo mysql -e "CREATE USER IF NOT EXISTS 'sakib'@'localhost' IDENTIFIED BY 'sakib';"
sudo mysql -e "CREATE DATABASE IF NOT EXISTS sqli;"
sudo mysql -e "GRANT ALL PRIVILEGES ON sqli.* TO 'sakib'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# 4. Import the Vulnerable Schema
echo -e "${GREEN}[+] Importing tables and seed data...${NC}"
# We navigate to the directory to ensure it finds setup.sql
cd /var/www/html/bugvault && sudo mysql -u sakib -psakib sqli < setup.sql

echo -e "\n${GREEN}=======================================${NC}"
echo -e "${GREEN}[✔] Setup Complete Successfully!${NC}"
echo -e "${GREEN}=======================================${NC}"
echo -e "🎯 Target URL: ${BLUE}http://localhost/bugvault/${NC}"
echo -e "🔥 You can now start firing your CLI tools (SQLMap, cURL) and browser payloads!"
