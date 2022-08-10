// config values could come from Google Sheets
// create plugin that connects to Google Sheets API
// shortcode when you want to use thermometer
// with params for the config values here
//
// let config = {
//     campaignName: 'Keep Wisdom Alive',
//     targetAmount: 50000,
//     currentAmount: 42090,
//     startingAmount: 0,
//     currency: "USD"
// };
//
console.log('+14 config: ', config);

//const range = document.querySelector("input[type='range']");
const formattedRange = config.currentAmount.toLocaleString('en-US', {
    style: 'currency',
    currency: config.currency,
});

const temperature = document.getElementsByClassName("progress-meter-temperature")[0];
const thermoHeader = document.getElementsByClassName("progress-meter-thermo-heading")[0];
thermoHeader.innerHTML += config.campaignName;

function setTemperature() {
    temperature.style.height = (config.currentAmount - config.startingAmount) / (config.targetAmount - config.startingAmount) * 100 + "%";
    //temperature.dataset.value = "$" + range + units[config.unit];
    temperature.dataset.value = formattedRange;
}

//range.addEventListener("input", setTemperature);
setTimeout(setTemperature, 1000);
