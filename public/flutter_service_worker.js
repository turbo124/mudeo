'use strict';
const CACHE_NAME = 'flutter-app-cache';
const RESOURCES = {
  "assets/AssetManifest.json": "53afc39e95d75486d1f0a650258657d1",
"assets/assets/images/apple.png": "4c5009357296781f833c380028ea719a",
"assets/assets/images/google.png": "da7edc6df70cada19994b8fba435e38a",
"assets/assets/images/logo-dark.png": "f01cd5dfdd987e5474cc96e9308af56b",
"assets/assets/images/logo-light.png": "a3fe389296bf4a00f3cffd305fddb74f",
"assets/assets/posenet_mv1_075_float_from_checkpoints.tflite": "e0c83d992292731a3f2dfe387d1470a6",
"assets/FontManifest.json": "5fa2baa1355ee1ffd882bec6ab6780c7",
"assets/fonts/MaterialIcons-Regular.ttf": "56d3ffdef7a25659eab6a68a3fbfaf16",
"assets/LICENSE": "be09bbcd3676f3fbb5cf81da3aa0a261",
"assets/packages/font_awesome_flutter/lib/fonts/fa-brands-400.ttf": "5a37ae808cf9f652198acde612b5328d",
"assets/packages/font_awesome_flutter/lib/fonts/fa-regular-400.ttf": "2bca5ec802e40d3f4b60343e346cedde",
"assets/packages/font_awesome_flutter/lib/fonts/fa-solid-900.ttf": "2aa350bd2aeab88b601a593f793734c0",
"favicon.png": "5dcef449791fa27946b3d35ad8803796",
"icons/Icon-192.png": "ac9a721a12bbc803b44f645561ecb1e1",
"icons/Icon-512.png": "96e752610906ba2a93c65f8abe1645f1",
"index.html": "ce4db044adeb59677d5256f951240a08",
"/": "ce4db044adeb59677d5256f951240a08",
"main.dart.js": "ee654bc62d5c7fad002879d215f06a16",
"manifest.json": "2029e9f86a9559a5c109661cbe207dee"
};

self.addEventListener('activate', function (event) {
  event.waitUntil(
    caches.keys().then(function (cacheName) {
      return caches.delete(cacheName);
    }).then(function (_) {
      return caches.open(CACHE_NAME);
    }).then(function (cache) {
      return cache.addAll(Object.keys(RESOURCES));
    })
  );
});

self.addEventListener('fetch', function (event) {
  event.respondWith(
    caches.match(event.request)
      .then(function (response) {
        if (response) {
          return response;
        }
        return fetch(event.request);
      })
  );
});
