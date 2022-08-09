// config values could come from Google Sheets
// create plugin that connects to Google Sheets API
// shortcode when you want to use thermometer
// with params for the config values here
const config = {
    campaignName: 'Keep Wisdom Alive',
    startingAmount: 0,
    targetAmount: 50000,
    currentAmount: 42090,
    currency: "USD"
};

//const range = document.querySelector("input[type='range']");
const formattedRange = config.currentAmount.toLocaleString('en-US', {
    style: 'currency',
    currency: config.currency,
});

const temperature = document.getElementById("progress-meter-temperature");
const thermoHeader = document.getElementById("progress-meter-thermo-heading");
thermoHeader.innerHTML += config.campaignName;

function setTemperature() {
    temperature.style.height = (config.currentAmount - config.startingAmount) / (config.targetAmount - config.startingAmount) * 100 + "%";
    //temperature.dataset.value = "$" + range + units[config.unit];
    temperature.dataset.value = formattedRange;
}

//range.addEventListener("input", setTemperature);
setTimeout(setTemperature, 1000);
