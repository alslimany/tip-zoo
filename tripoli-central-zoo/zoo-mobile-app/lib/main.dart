import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:zoo_mobile_app/providers/animal_provider.dart';
import 'package:zoo_mobile_app/providers/facility_provider.dart';
import 'package:zoo_mobile_app/providers/activity_provider.dart';
import 'package:zoo_mobile_app/providers/sync_provider.dart';
import 'package:zoo_mobile_app/screens/home/home_screen.dart';
import 'package:zoo_mobile_app/services/database_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Initialize local database
  await DatabaseService.instance.database;
  
  runApp(const TripolitZooApp());
}

class TripolitZooApp extends StatelessWidget {
  const TripolitZooApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AnimalProvider()),
        ChangeNotifierProvider(create: (_) => FacilityProvider()),
        ChangeNotifierProvider(create: (_) => ActivityProvider()),
        ChangeNotifierProvider(create: (_) => SyncProvider()),
      ],
      child: MaterialApp(
        title: 'Tripoli Central Zoo',
        debugShowCheckedModeBanner: false,
        theme: ThemeData(
          colorScheme: ColorScheme.fromSeed(
            seedColor: const Color(0xFF2E7D32),
            brightness: Brightness.light,
          ),
          useMaterial3: true,
          appBarTheme: const AppBarTheme(
            centerTitle: true,
            elevation: 0,
          ),
        ),
        home: const HomeScreen(),
      ),
    );
  }
}
