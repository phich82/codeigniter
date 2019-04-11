# **1. Install NodeJS**
	- Access to link: https://nodejs.org/en/
	- Download NodeJS by your OS
	- After downloading, double-click on this file for installing NodeJS

# **2. Install Appium**
	npm install -g appium

# **3. Driver-Specific Setup**
   - The XCUITest Driver (for iOS apps)
   - The UiAutomator2 Driver (for Android apps)
   - => http://appium.io/docs/en/drivers/android-uiautomator2/index.html
   - The Windows Driver (for Windows Desktop apps)
   - The Mac Driver (for Mac Desktop apps)
   - (BETA) The Espresso Driver (for Android apps)

# **4. Verify the needed tools when using Appium**
###    **- Install appium-doctor:**
        npm install -g appium-doctor
###    **- Run the following command for verifying:**
	    - For android:
            appium-doctor --android

        - For IOs:
	        appium-doctor --ios

###    **- Install opencv4nodejs:**
        + Install cmake (cmake-3.13.2-win64-x64.msi: https://cmake.org/download/)
        + npm install --global windows-build-tools
        + npm i -g opencv4nodejs

# **5. Install the language binding for Appium**
        - Install composer or download composer.phar

        - Install the package for php language:
	        composer require-dev php-client
	        composer.phar require-dev php-client

# **6. Start appium**
    - From command line: type the command below & press ENTER
        appium
    - From desktop (if installed):
        Click on Appium icon
