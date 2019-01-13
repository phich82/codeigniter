# **1. Install Appium**
	npm install -g appium

# **2. Driver-Specific Setup**
   - The XCUITest Driver (for iOS apps)
   - The UiAutomator2 Driver (for Android apps)
   - => http://appium.io/docs/en/drivers/android-uiautomator2/index.html
   - The Windows Driver (for Windows Desktop apps)
   - The Mac Driver (for Mac Desktop apps)
   - (BETA) The Espresso Driver (for Android apps)

# **3. Verifying the Installation**
###    **- Install appium-doctor:**
        npm install -g appium-doctor
###    **- Run command for verifying:**
	    appium-doctor --android
	    appium-doctor --ios

###    **- Install opencv4nodejs:**
        + Install cmake (cmake-3.13.2-win64-x64.msi: https://cmake.org/download/)
        + npm install --global windows-build-tools
        + npm i -g opencv4nodejs

# **4. Appium Clients**
	=> php-client (php language)

# **5. Start appium**
    - From command line: type the command below & press ENTER
        appium
    - From desktop if installed:
        Click on Appium icon
