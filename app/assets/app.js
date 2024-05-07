import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
//import 'leaflet/dist/leaflet.min.css'

import angular from 'angular'
import L from 'leaflet'

let  app = angular.module('myApp', []);
app
    .config(function($interpolateProvider) {
        $interpolateProvider.startSymbol('[[');
        $interpolateProvider.endSymbol(']]');
    })
    .controller('myCtrl', function($http, $interval,$scope) {

        $scope.data = {
            count: 0,
            last: "",
            data: []
        };

        // Function to retrieve data from the server
        function getData() {
            $http.get('http://localhost:9000/dashboard')
                .then(function(response) {
                    $scope.data = response.data;

                    for (let i = 0; i < $scope.data.data.length; i++) {
                        $scope.data.data[i] = JSON.parse($scope.data.data[i]);
                    }
                    console.log('Data Retrieved')
                })
                .catch(function(error) {
                    console.error('Error retrieving data:', error);
                });
        }

        // Call getData() immediately
        getData();

        // Call getData() every minute (60000 milliseconds)
        $interval(getData, 10 * 1000);
    });


const map = L.map('map', {
    dragging: false,
}).setView([5.495492, -4.061254], 13);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    minZoom: 15,
    attribution: 'CocoaShield Dashbaord v0.0.1'
}).addTo(map);
L.marker([5.495492, -4.061254]).addTo(map);