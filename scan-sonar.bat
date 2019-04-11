@echo off

set _SONAR_SCANNER="C:\tooltest\sonar\sonar-scanner-3.3.0.1492\bin\sonar-scanner.bat"
set _PROJECT_ROOT_PATH="C:\Users\huynhphat\Desktop\web\learning\php\CodeIgniter\codeigniter"
set _SOURCES_PATH=".\webapp\controllers\requests"
set _PROJECT_KEY="my-project"

REM  Get path from cli if entered (%1: 2nd argument)
if not "%1" == "" set _SOURCES_PATH="%1"

echo INFO: Source dir: %_SOURCES_PATH%

%_SONAR_SCANNER% -Dsonar.projectKey=%_PROJECT_KEY% -Dsonar.projectBaseDir=%_PROJECT_ROOT_PATH% -Dsonar.sources=%_SOURCES_PATH%
