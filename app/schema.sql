CREATE TABLE agents (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    slug TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    title TEXT,
    phone TEXT,
    email TEXT,
    photo TEXT,
    bio TEXT,
    experience_years INTEGER DEFAULT 0,
    languages TEXT,
    specialties TEXT,
    sort INTEGER DEFAULT 0
);

CREATE TABLE listings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    slug TEXT NOT NULL UNIQUE,
    title TEXT NOT NULL,
    type TEXT NOT NULL DEFAULT 'satilik',
    category TEXT NOT NULL DEFAULT 'daire',
    price REAL NOT NULL DEFAULT 0,
    city TEXT DEFAULT 'İstanbul',
    district TEXT,
    neighborhood TEXT,
    lat REAL,
    lng REAL,
    rooms TEXT,
    gross_m2 INTEGER,
    net_m2 INTEGER,
    bathrooms INTEGER,
    floor TEXT,
    total_floors INTEGER,
    building_age INTEGER,
    heating TEXT,
    description TEXT,
    features TEXT DEFAULT '[]',
    images TEXT DEFAULT '[]',
    agent_id INTEGER REFERENCES agents(id) ON DELETE SET NULL,
    featured INTEGER DEFAULT 0,
    status TEXT DEFAULT 'aktif',
    views INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_listings_filter ON listings(status, type, category, district);

CREATE TABLE posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    slug TEXT NOT NULL UNIQUE,
    title TEXT NOT NULL,
    category TEXT,
    excerpt TEXT,
    content TEXT,
    cover TEXT,
    author TEXT,
    published INTEGER DEFAULT 1,
    published_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT,
    phone TEXT,
    subject TEXT,
    message TEXT,
    listing_id INTEGER REFERENCES listings(id) ON DELETE SET NULL,
    is_read INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    name TEXT
);

CREATE TABLE settings (
    key TEXT PRIMARY KEY,
    value TEXT
);
