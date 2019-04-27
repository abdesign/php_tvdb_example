/**
 *  Imports are a little heavy, needs optimization
 *  Everything included using webpack to save time.
 */

import 'bootstrap/js/dist/button';
import 'bootstrap/js/dist/util';
import 'bootstrap/js/dist/dropdown';
import 'bootstrap/js/dist/alert';
import 'bootstrap/js/dist/index';
import '../scss/app.scss';

// Button Behavior for Search
jQuery('#search').click(() => {
  jQuery('#results').html('');
  jQuery('.overlay').show();
  searchSeriesName(jQuery('#search-form').val());
});

// Button Behavior for Showing the Search Header when Hidden
jQuery('#show-search').click(() => {
  jQuery('#show-search').hide();
  jQuery('#search-header').show();
});


/**
 * Class to create the Series Entity
 */
class Series
{

  /**
   * Constructor
   * @param {[Int]} id            [Record id of the Series]
   * @param {[Int]} tvdbId        [TheTVDB Id]
   * @param {[String]} seriesName [Name of the series]
   * @param {[String]} overview   [Overview/Description of the Series]
   * @param {[String]} thumbnail  [Thumnail Poster Image]
   * @param {[String]} image      [Full Size Poser Image]
   * @param {[String]} imdbId     [IMDB Id]
   * @param {[String]} network    [Network Name]
   */
  constructor(id, tvdbId, seriesName, overview, thumbnail, image, imdbId, network)
  {
    this._id = id || '';
    this._tvdbId = tvdbId || '';
    this._imdbId = imdbId || '';
    this._seriesName = seriesName || '';
    this._overview = overview || '';
    this._image = image || '';
    this._thumbnail = thumbnail || '';
    this._network = network || '';

    this._template = jQuery('#series-results > .series-container');
  }

  /**
   * Adds the templated Series to the DOM using jQuery
   */
  addSeriesTemplate()
  {
    var seriesContainer = 'series-container-'+this._id;
    var showHtml = this._template.clone(this._template).appendTo('#results').attr('id', seriesContainer);

    var button = jQuery('#'+seriesContainer).find('.series-button');

    button.attr('id', 'btn-'+this._id).click(() => {
      console.log(jQuery(this));
      console.log('Series Id: '+this._id);
      button.prop("disabled",true);
      getEpisodes(this._id);
    });

    jQuery('#'+seriesContainer).find('.series-title').html(this._seriesName);

    if(this._imdbId) jQuery('#'+seriesContainer).find('.series-imdb-link').attr('href','https://www.imdb.com/title/'+this._imdbId);
    if(this._overview) jQuery('#'+seriesContainer).find('.series-overview').html(this._overview);
    if(this._network) jQuery('#'+seriesContainer).find('.series-network').html(this._network);

    if(this._thumbnail)
    {
      jQuery('#'+seriesContainer).find('.series-poster-thumb').attr('src','/img/TVDB/posters/'+this._thumbnail);
    }else{
      jQuery('#'+seriesContainer).find('.series-poster-thumb').attr('src','/img/TVDB/posters/thumbnail_default.jpg');
    }

  }
}

/**
 * Class to create the Series Entity
 */
class Episode
{
  /**
   * Constructor
   * @param {[Int]} id                    [Record id of the Episode]
   * @param {[Int]} seriesId              [Series id that the Episode Belongs to]
   * @param {[Int]} tvdbId                [TheTVDB Id]
   * @param {[String]} episodeName        [Name of the Episode]
   * @param {[String]} overview           [Overview/Description]
   * @param {[String]} image              [Image for the Episode]
   * @param {[Int]} airedSeason           [Season that the Episode Aired]
   * @param {[Int]} airedEpisodeNumber    [Episode Number]
   */
  constructor(id, seriesId, tvdbId, episodeName, overview, image, airedSeason, airedEpisodeNumber)
  {
    this._id = id || '';
    this._seriesId = seriesId || '';
    this._tvdbId = tvdbId || '';
    this._episodeName = episodeName || '';
    this._overview = overview || '';
    this._image = image || '';
    this._airedSeason = airedSeason || '';
    this._airedEpisodeNumber = airedEpisodeNumber || '';

    this._template = jQuery('#episode-results > .episode-container');
  }

  /**
   * Adds the templated Episode to the DOM using jQuery
   */
  addEpisodeTemplate()
  {

    var episodeContainer = 'episode-container-'+this._id;
    var showHtml = this._template.clone(this._template).attr('id', episodeContainer).appendTo('#series-container-'+this._seriesId);

    jQuery('#'+episodeContainer).find('.episode-title').html(this._episodeName);

    if(this._overview) jQuery('#'+episodeContainer).find('.episode-overview').html(this._overview);
    if(this._airedSeason) jQuery('#'+episodeContainer).find('.episode-season').html(this._airedSeason);
    if(this._airedEpisodeNumber) jQuery('#'+episodeContainer).find('.episode-number').html(this._airedEpisodeNumber);

    if(this._image)
    {
      jQuery('#'+episodeContainer).find('.episode-img').attr('src','/img/TVDB/episodes/'+this._image);
    }else{
      jQuery('#'+episodeContainer).find('.episode-img').attr('src','/img/TVDB/episodes/default.jpg');
    }

  }
}

/**
 * Gets the Episodes Belonging to a Series using a JSON API Call to Local API
 * @param  {[Int]} seriesId [Id of the Series]
 * @return None
 */
const getEpisodes = (seriesId) => {

  console.log("Getting Episodes for: " + seriesId);

  if (seriesId) {

    jQuery.getJSON('/api/series/episodes/'+escape(seriesId), (data)=>{

      var episodeCollection = [];

      data.forEach((episode) => {
        episodeCollection.push(new Episode(
          episode.id,
          episode.seriesId,
          episode.tvdbId,
          episode.episodeName,
          episode.overview,
          episode.image,
          episode.airedSeason,
          episode.airedEpisodeNumber
        ));
      });

      console.log(episodeCollection);

      episodeCollection.forEach((Episode) => {
        Episode.addEpisodeTemplate();
      })
    });
  }
}

/**
 * Searches for a Series using a JSON API Call to Local API
 * @param  {[String]} seriesName [Name of the Series to Search]
 * @return None
 */
const searchSeriesName = (seriesName) => {

  console.log("Searching for: " + seriesName);

  if (seriesName) {

    jQuery.getJSON('/api/search/'+escape(seriesName), (data)=>{

      var seriesCollection = [];

      data.forEach((series) => {
        seriesCollection.push(new Series(
          series.id,
          series.tvdbId,
          series.seriesName,
          series.overview,
          series.thumbnail,
          series.image,
          series.imdbId,
          series.network,
        ));
      });

      console.log(seriesCollection);

      seriesCollection.forEach((Series) => {
        Series.addSeriesTemplate();
      })

      jQuery('.overlay').hide();
      jQuery('#show-search').show();
      jQuery('#search-header').hide();

    });
  }
}

$(document).keypress((event) => {
  if (event.which == '13') {
    event.preventDefault();
  }
});
