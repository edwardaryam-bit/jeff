const { MongoClient } = require('mongodb');
const bcrypt = require('bcryptjs');

const mongoUri = process.env.MONGO_URI || 'mongodb://127.0.0.1:27017';
const organizationId = process.env.MONGO_ORG_ID || '69dd5dc463b513e109c19572';
const mongoDbName = process.env.MONGO_DB || organizationId;

const client = new MongoClient(mongoUri, { useUnifiedTopology: true });

const initialUsers = [
  {
    name: 'System Admin',
    email: 'admin@ewaste.com',
    role: 'admin',
    password: 'admin123'
  },
  {
    name: 'John Boda',
    email: 'john@boda.com',
    role: 'driver',
    vehicle_type: 'Boda boda',
    status: 'inactive',
    password: 'password123'
  },
  {
    name: 'Timothy Tuku',
    email: 'tim@tuku.com',
    role: 'driver',
    vehicle_type: 'Tuku Tuku',
    status: 'inactive',
    password: 'password123'
  },
  {
    name: 'Paul Pickup',
    email: 'paul@pickup.com',
    role: 'driver',
    vehicle_type: 'Pickup',
    status: 'inactive',
    password: 'password123'
  },
  {
    name: 'Fred Truck',
    email: 'fred@truck.com',
    role: 'driver',
    vehicle_type: 'Truck',
    status: 'inactive',
    password: 'password123'
  },
  {
    name: 'Alice Customer',
    email: 'alice@customer.com',
    role: 'user',
    password: 'password123'
  }
];

async function seed() {
  await client.connect();
  const db = client.db(mongoDbName);
  const users = db.collection('users');
  await users.createIndex({ email: 1 }, { unique: true });

  for (const user of initialUsers) {
    const hashedPassword = bcrypt.hashSync(user.password, 10);
    const existing = await users.findOne({ email: user.email.toLowerCase() });
    if (existing) {
      await users.updateOne(
        { _id: existing._id },
        {
          $set: {
            name: user.name,
            role: user.role,
            vehicle_type: user.vehicle_type || null,
            status: user.status || null,
            password: hashedPassword,
            updated_at: new Date()
          }
        }
      );
      console.log(`Updated user: ${user.email}`);
    } else {
      await users.insertOne({
        name: user.name,
        email: user.email.toLowerCase(),
        role: user.role,
        vehicle_type: user.vehicle_type || null,
        status: user.status || null,
        password: hashedPassword,
        created_at: new Date()
      });
      console.log(`Created user: ${user.email}`);
    }
  }

  console.log('Seeding completed for MongoDB database:', mongoDbName);
  await client.close();
}

seed().catch((err) => {
  console.error('Migration failed:', err);
  process.exit(1);
});
