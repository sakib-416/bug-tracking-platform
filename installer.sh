#!/usr/bin/env bash
# installer.sh

# Terminal Color Codes
RED='\033[0;31m'
GREEN='\033[0;32m'
CYAN='\033[0;36m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

clear
echo -e "${CYAN}===============================================================${NC}"
echo -e "${CYAN}        GLASSBUG VULNERABLE LAB WORKSPACE INSTALLATION         ${NC}"
echo -e "${CYAN}===============================================================${NC}"

# Step 1: Environment Verification Matrix
echo -e "\n${YELLOW}[*] Validating local environment execution runtimes...${NC}"
if ! command -v php &> /dev/null; then
    echo -e "${RED}[-] Error: PHP runtime binary not found on local path systems.${NC}"
    echo -e "${YELLOW}[!] Install PHP (including php-sqlite3 package extensions) to proceed.${NC}"
    exit 1
else
    echo -e "${GREEN}[+] PHP Engine detected: $(php -v | head -n 1)${NC}"
fi

# Step 2: Provision Directory Paths & File Locks
echo -e "\n${YELLOW}[*] Configuring local storage matrices...${NC}"
mkdir -p uploads logs attacker-lab
chmod 777 uploads logs 2>/dev/null || true

# Step 3: Parse SQLite Database Generation Pipeline
echo -e "\n${YELLOW}[*] Building dynamic SQLite data infrastructure...${NC}"
if [ -f "database.sqlite" ]; then
    echo -e "${YELLOW}[!] Existing tracking database detected. Purging previous telemetry states...${NC}"
    rm database.sqlite
fi

# Use internal PHP parsing structures to guarantee zero dependency conversions across environments
php -r "
try {
    \$db = new PDO('sqlite:database.sqlite');
    \$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    \$sql = file_get_contents('schema.sql');
    \$db->exec(\$sql);
    echo 'Database initialized and seeded successfully.';
} catch (Exception \$e) {
    echo 'Error compiling database context: ' . \$e->getMessage();
    exit(1);
}
"
if [ $? -ne 0 ]; then
    echo -e "\n${RED}[-] Critical Database provisioning abstraction anomaly occurred.${NC}"
    exit 1
fi

echo -e "\n${GREEN}[+] Application workspace architecture successfully finalized.${NC}"
echo -e "${CYAN}===============================================================${NC}"
echo -e "${GREEN}  GlassBug Security Lab Environment Ready for Local Deployment!  ${NC}"
echo -e "${CYAN}===============================================================${NC}"
echo -e "  Target URI:       ${YELLOW}http://localhost:8080${NC}"
echo -e "  Attacker CSRF Domain:  ${YELLOW}http://localhost:8080/attacker-lab/csrf_exploit.html${NC}"
echo -e "${CYAN}===============================================================${NC}"
echo -e "${YELLOW}[*] Launching local multi-threaded development web routing engine...${NC}"
echo -e "${YELLOW}[*] Press [CTRL+C] to terminate the laboratory deployment instance.${NC}"

# Spin up the native PHP server inside the current workspace context directory root directly
php -S localhost:8080
