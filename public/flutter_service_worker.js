'use strict';
const CACHE_NAME = 'flutter-app-cache';
const RESOURCES = {
  "/assets/AssetManifest.json": "2f4896925f8a54e1a9a6a1d2e3f48a63",
"/assets/assets/images/apple.png": "4c5009357296781f833c380028ea719a",
"/assets/assets/images/google.png": "da7edc6df70cada19994b8fba435e38a",
"/assets/assets/images/logo-dark.png": "f01cd5dfdd987e5474cc96e9308af56b",
"/assets/assets/images/logo-light.png": "a3fe389296bf4a00f3cffd305fddb74f",
"/assets/FontManifest.json": "667bf07c9f50ab329c7c28259428c7a4",
"/assets/fonts/MaterialIcons-Regular.ttf": "56d3ffdef7a25659eab6a68a3fbfaf16",
"/assets/LICENSE": "8a7dd93d1f7819d33e82b8e110394a44",
"/assets/packages/font_awesome_flutter/lib/fonts/fa-brands-400.ttf": "5a37ae808cf9f652198acde612b5328d",
"/assets/packages/font_awesome_flutter/lib/fonts/fa-regular-400.ttf": "2bca5ec802e40d3f4b60343e346cedde",
"/assets/packages/font_awesome_flutter/lib/fonts/fa-solid-900.ttf": "2aa350bd2aeab88b601a593f793734c0",
"/assets/packages/open_iconic_flutter/assets/open-iconic.woff": "3cf97837524dd7445e9d1462e3c4afe2",
"/favicon.png": "5dcef449791fa27946b3d35ad8803796",
"/icons/Icon-192.png": "ac9a721a12bbc803b44f645561ecb1e1",
"/icons/Icon-512.png": "96e752610906ba2a93c65f8abe1645f1",
"/index.html": "74181cae89ef68b21f05cc3f04a0782d",
"/main.dart.js": "60d0779dd51b67c4851f1efd8ce3a4d5",
"/manifest.json": "4d1a058a269ee4117eb21940d766182e"
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