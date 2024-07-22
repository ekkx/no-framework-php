BEGIN
TRANSACTION;

CREATE TABLE `users`
(
    `id`         INTEGER PRIMARY KEY AUTOINCREMENT,
    `username`   TEXT NOT NULL,
    `email`      TEXT NOT NULL,
    `password`   TEXT NOT NULL,
    `createdAt` TIMESTAMP NOT NULL DEFAULT (DATETIME('now', 'localtime')),
    `updatedAt` TIMESTAMP NOT NULL DEFAULT (DATETIME('now', 'localtime')),
    `lastLoginAt` TIMESTAMP NOT NULL DEFAULT (DATETIME('now', 'localtime'))
);

COMMIT;
