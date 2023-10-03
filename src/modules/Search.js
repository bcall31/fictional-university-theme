import $ from "jquery";


class Search {
    // 1. Initialize
    constructor() {
        this.addSearchHTML();
        this.openButton = $(".js-search-trigger");
        this.closeButton = $(".search-overlay__close");
        this.searchOverlay = $(".search-overlay");
        
        this.resultsDiv = $("#search-overlay__results");
        this.searchField = $("#search-term");

        this.events();
        this.isOpenOverlay = false;
        this.isSpinnerVisible = false;
        this.typingTimer;

        this.previousValue;
    }

    // 2. Events
    events() {
        this.openButton.on("click", this.openOverlay.bind(this));
        this.closeButton.on("click", this.closeOverlay.bind(this));
        $(document).on("keydown", this.keyPressDispatcher.bind(this));
        this.searchField.on("keyup", this.typingLogic.bind(this));
    }

    // 3. Methods
    typingLogic() {
        if (this.searchField.val() != this.previousValue) {
            clearTimeout(this.typingTimer);

            if (this.searchField.val()) {
                if (!this.isSpinnerVisible) {
                    this.resultsDiv.html("<div class='spinner-loader'></div>");
                    this.isSpinnerVisible = true;
                }
                this.typingTimer = setTimeout(this.getResults.bind(this), 500);
            } else {
                this.resultsDiv.html("");
                this.isSpinnerVisible = false;
            }
        }
        this.previousValue = this.searchField.val();
    }

    getResults() {
        $.getJSON(universityData.root_url + '/wp-json/university/v1/search?term=' + this.searchField.val(), (data) => {
            this.resultsDiv.html(`
                <div class='row'>
                    <div class='one-third'>
                        <h2 class="search-overlay__section-title">General Information</h2>
                            ${data.generalInfo.length ? '<ul class="link-list min-list">' : '<p>No general information found.</p>'}
                                ${data.generalInfo.map(item => `<li><a href="${item.permalink}">${item.title}</a>${item.postType == "post" ? ` by ${item.authorName}` : ""}</li>`).join("")}
                            ${data.generalInfo.length ? '</ul>' : ''}
                    </div>
                    <div class='one-third'>
                        <h2 class="search-overlay__section-title">Programs</h2>
                            ${data.programs.length ? '<ul class="link-list min-list">' : `<p>No programs found. <a href=${universityData.root_url}/programs">View all programs.</a></p>`}
                                ${data.programs.map(item => `<li><a href="${item.permalink}">${item.title}</a>${item.postType == "post" ? ` by ${item.authorName}` : ""}</li>`).join("")}
                            ${data.programs.length ? '</ul>' : ''}
                        <h2 class="search-overlay__section-title">Professors</h2>
                            ${data.professors.length ? '<ul class="professor-cards">' : '<p>No professors found.</p>'}
                                ${data.professors.map(item => `
                                    <li class="professor-card list-item">
                                        <a href="${item.permalink}">
                                            <img class="professor-card__image" src="${item.image}" alt="${item.title}" />
                                            <span class="professor-card__name">${item.title}</span>
                                        </a>
                                    </li>
                                `).join("")}
                            ${data.professors.length ? '</ul>' : ''}
                    </div>
                    <div class='one-third'>
                        <h2 class="search-overlay__section-title">Campuses</h2>
                            ${data.campuses.length ? '<ul class="link-list min-list">' : `<p>No campuses found. <a href=${universityData.root_url}/campuses">View all campuses.</a></p>`}
                                ${data.campuses.map(item => `<li><a href="${item.permalink}">${item.title}</a>${item.postType == "post" ? ` by ${item.authorName}` : ""}</li>`).join("")}
                            ${data.campuses.length ? '</ul>' : ''}

                        <h2 class="search-overlay__section-title">Events</h2>
                            ${data.events.length ? '<ul class="link-list min-list">' : `<p>No events found. <a href=${universityData.root_url}/events">View all events.</a></p>`}
                                ${data.events.map(item => `
                                <div class="event-summary">
                                    <a class="event-summary__date t-center" href="${item.permalink}">
                                    <span class="event-summary__month">
                                        ${item.month}
                                    </span>
                                    <span class="event-summary__day">
                                        ${item.day}
                                    </span>
                                    </a>
                                    <div class="event-summary__content">
                                    <h5 class="event-summary__title headline headline--tiny"><a href="${item.permalink}">${item.title}</a></h5>
                                    <p>
                                        ${item.description}
                                        <a href="${item.permalink}" class="nu gray">Learn more</a>
                                    </p>
                                    </div>
                                </div>
                                `).join("")}
                            ${data.events.length ? '</ul>' : ''}
                    </div>
                </div>
            `);
            this.isSpinnerVisible = false;
        })
    }

    keyPressDispatcher(e) {
        if (e.keyCode == 83 && !this.isOpenOverlay && !$("input, textarea").is(":focus")) {
            this.openOverlay();
        }
        if (e.keyCode == 27 && this.isOpenOverlay) {
            this.closeOverlay();
        }
    }

    openOverlay() {
        this.searchOverlay.addClass("search-overlay--active");
        $("body").addClass("body-no-scroll");
        this.searchField.val('');
        setTimeout(() => this.searchField.focus(), 301);
        this.isOpenOverlay = true;
    }

    closeOverlay() {
        this.searchOverlay.removeClass("search-overlay--active");
        $("body").removeClass("body-no-scroll");
        this.isOpenOverlay = false;
    }

    addSearchHTML() {
        $("body").append(`
        <div class="search-overlay">
            <div class="search-overlay__top">
                <div class="container">
                <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
                <input 
                    type="text" 
                    class="search-term" 
                    placeholder="What are you looking for?"
                    id="search-term">
                <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
                </div>
            </div>
            <div class="container">
                <div id="search-overlay__results">
                
                </div>
            </div>
        </div>
        `);
    }
    
}

export default Search